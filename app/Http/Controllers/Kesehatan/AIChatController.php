<?php

namespace App\Http\Controllers\Kesehatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AIChatController extends Controller
{
    public function index()
    {
        $hasApiKey = !empty(getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? ($_SERVER['GEMINI_API_KEY'] ?? config('services.gemini.key'))));
        return view('kesehatan.ai_chat.index', compact('hasApiKey'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = trim($request->message);
        $lowercaseMessage = strtolower($message);

        // --- 1. INTEGRASI GEMINI API (JIKA KUNCI TERSEDIA) ---
        $apiKey = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? ($_SERVER['GEMINI_API_KEY'] ?? config('services.gemini.key')));
        if ($apiKey) {
            try {
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => "Anda adalah Asisten AI Kesehatan pintar untuk Puskesmas. Jawab pertanyaan pengguna berikut: \"{$message}\".
                                        
Ketentuan Jawaban:
1. Periksa apakah pertanyaan berhubungan dengan kesehatan, medis, penyakit, imunisasi, obat, gizi, gaya hidup sehat, pertolongan pertama, atau dampak buruk zat/kebiasaan seperti rokok/alkohol/narkoba.
2. Jika TIDAK berhubungan dengan kesehatan, berikan tanggapan penolakan yang sopan dengan ikon peringatan kuning (<div class=\"ai-warning-alert\"><i class=\"ri-alert-line\"></i> Maaf, sebagai Asisten AI Kesehatan...</div>).
3. Anda WAJIB memberikan jawaban dalam format JSON mentah (jangan dibungkus markdown ```json) dengan struktur berikut:
{
  \"thinking\": [
    \"Langkah analisis 1 dalam bahasa Indonesia\",
    \"Langkah analisis 2 dalam bahasa Indonesia\",
    ...
  ],
  \"reply\": \"Isi jawaban lengkap dalam format HTML bersih (gunakan tag <h3>, <p>, <ul>, <li>, <strong>, dan kelas ikon Remix Icon seperti <i class='ri-capsule-line'></i> atau <i class='ri-alert-line'></i>)\"
}
4. Jawablah dalam Bahasa Indonesia yang ramah, profesional, dan mudah dipahami warga puskesmas."
                                    ]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json',
                            'temperature' => 0.2
                        ]
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                        $textResult = trim($json['candidates'][0]['content']['parts'][0]['text']);
                        
                        // Bersihkan pembungkus markdown ```json jika ada
                        if (str_starts_with($textResult, '```')) {
                            $textResult = preg_replace('/^```(?:json)?/i', '', $textResult);
                            $textResult = preg_replace('/```$/i', '', $textResult);
                            $textResult = trim($textResult);
                        }
                        
                        $decoded = json_decode($textResult, true);
                        if ($decoded && isset($decoded['thinking']) && isset($decoded['reply'])) {
                            return response()->json([
                                'thinking' => $decoded['thinking'],
                                'reply' => $decoded['reply']
                            ]);
                        }
                    }
                }
                
                // Return API error to the chat interface for easy debugging
                return response()->json([
                    'thinking' => ["Mendeteksi kegagalan respon dari API Gemini"],
                    'reply' => '<div class="ai-warning-alert"><i class="ri-alert-line"></i> API Gemini mengembalikan error (' . $response->status() . '): ' . e($response->body()) . '</div>'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'thinking' => ["Terjadi kesalahan koneksi ke API Gemini"],
                    'reply' => '<div class="ai-warning-alert"><i class="ri-alert-line"></i> Koneksi gagal: ' . e($e->getMessage()) . '</div>'
                ]);
            }
        }

        // --- 2. FALLBACK KE MESIN MATRIKS KATA KUNCI LOKAL ---
        // General health keywords list for validation
        $healthKeywords = [
            // Kata Tanya & Aksi Medis Umum
            'sehat', 'sakit', 'penyakit', 'gejala', 'cegah', 'pencegahan', 'obat', 'medis', 
            'dokter', 'imunisasi', 'vaksin', 'kesehatan', 'kipi', 'demam', 'batuk', 'pilek', 
            'flu', 'pusing', 'diare', 'tbc', 'tuberkulosis', 'hepatitis', 'polio', 'campak', 
            'rubella', 'dbd', 'malaria', 'diabetes', 'hipertensi', 'asma', 'kolesterol', 
            'cacar', 'lambung', 'kanker', 'jantung', 'infeksi', 'virus', 'bakteri', 'luka', 
            'nyeri', 'mual', 'muntah', 'imun', 'terapi', 'resep', 'tensi', 'puskesmas',
            'ambulan', 'darurat', 'kesehatan', 'terinfeksi', 'vaksinasi', 'lelah', 'gigi', 
            'stress', 'stres', 'cemas', 'anxiety', 'tidur', 'insomnia', 'diet', 'olahraga', 
            'gizi', 'berat badan', 'kalori', 'tulang', 'mata', 'kulit', 'alergi', 'gatal', 
            'bersin', 'hati', 'liver', 'hepar', 'maag', 'gerd', 'perih', 'kepala', 'migrain', 
            'vertigo', 'mencret', 'sesak', 'dada', 'nyeri dada', 'bengkak', 'alergen', 'ruam',
            'tumor', 'benjolan', 'karsinoma', 'darah tinggi', 'gula darah', 'kencing manis',
            'panas', 'pilek', 'bersin', 'luka bakar', 'nutrisi', 'diet sehat', 'patah hati',
            'kelelahan', 'saraf', 'sirosis', 'kuning', 'ulu hati', 'alkohol', 'rokok', 'nikotin',
            'miras', 'efek samping', 'efek', 'samping', 'bahaya', 'candu', 'kecanduan', 'narkoba',
            'ketergantungan', 'zat adiktif',
            
            // Tambahan Kata Terkait Kesehatan & Penyakit (Untuk Mendukung Pertanyaan Lebih Luas)
            'penyebab', 'mengapa', 'kenapa', 'bagaimana', 'cara', 'tips', 'mengatasi', 'mengobati',
            'mencegah', 'jerawat', 'acne', 'wajah', 'muka', 'komedo', 'beruntusan', 'ruam',
            'bintik', 'nanah', 'ngilu', 'linu', 'pegal', 'sendi', 'tulang', 'patah', 'retak',
            'keseleo', 'terkilir', 'kram', 'kejang', 'kaku', 'lumpuh', 'lemas', 'capek',
            'keliyengan', 'kembung', 'begah', 'sendawa', 'diare', 'bab', 'sembelit', 'wasir',
            'ambeyen', 'cacingan', 'keracunan', 'biduran', 'kaligata', 'bentol', 'eksim',
            'kurap', 'panu', 'kutu air', 'jamur', 'bisul', 'abses', 'lecet', 'memar',
            'lebam', 'melepuh', 'meriang', 'sumeng', 'selesma', 'tenggorokan', 'radang',
            'serak', 'amandel', 'gondongan', 'sinus', 'mengi', 'ngik', 'bronkitis',
            'pneumonia', 'ispa', 'covid', 'corona', 'anemia', 'kurang darah', 'ginjal',
            'batu ginjal', 'gagal ginjal', 'cuci darah', 'anyang-anyangan', 'kemih',
            'kencing', 'urin', 'usus', 'apendisit', 'hernia', 'wasir', 'stroke',
            'pikun', 'demensia', 'gemetar', 'epilepsi', 'ayan', 'begadang', 'tidur',
            'sariawan', 'mulut', 'bau mulut', 'gusi', 'belekan', 'katarak', 'glaukoma',
            'telinga', 'denging', 'budeg', 'mimisan', 'stunting', 'kurang gizi',
            'asi', 'menyusui', 'sufor', 'mpasi', 'posyandu', 'bumil', 'hamil',
            'kehamilan', 'janin', 'kandungan', 'usg', 'ketuban', 'melahirkan', 'persalinan',
            'kb', 'pil kb', 'suntik kb', 'iud', 'spiral', 'kondom', 'kontrasepsi',
            'haid', 'menstruasi', 'pms', 'keputihan', 'menopause', 'kista', 'miom',
            'tiroid', 'gondok', 'hiv', 'aids', 'sifilis', 'gonore', 'herpes', 'ims',
            'sabu', 'ganja', 'ekstasi', 'heroin', 'vape', 'tembakau', 'sakau',
            'rehabilitasi', 'vitamin', 'suplemen', 'mineral', 'serat', 'lemak',
            'protein', 'karbohidrat', 'obesitas', 'kegemukan', 'kebugaran', 'hidrasi',
            'air putih', 'phbs', 'sanitasi', 'cuci tangan', 'sabun', 'kebersihan'
        ];


        // Check if message is related to health
        $isHealthRelated = false;
        foreach ($healthKeywords as $keyword) {
            if (str_contains($lowercaseMessage, $keyword)) {
                $isHealthRelated = true;
                break;
            }
        }

        if (!$isHealthRelated) {
            $thinkingSteps = [
                "Menerima pesan: \"" . e($message) . "\"",
                "Menganalisis konten pesan untuk mendeteksi kata kunci kesehatan...",
                "Hasil analisis: Tidak mendeteksi kata kunci kesehatan atau medis yang relevan.",
                "Memformulasikan tanggapan penolakan secara sopan sesuai ruang lingkup asisten."
            ];

            return response()->json([
                'thinking' => $thinkingSteps,
                'reply' => '<div class="ai-warning-alert"><i class="ri-alert-line"></i> Maaf, sebagai <strong>Asisten AI Kesehatan</strong>, saya hanya dapat menjawab pertanyaan seputar kesehatan seperti deskripsi penyakit, gejala, langkah pencegahan, pengobatan, atau imunisasi anak. Silakan ajukan pertanyaan yang berkaitan dengan kesehatan.</div>'
            ]);
        }

        // Detailed health topics database
        $topics = [
            'alkohol_rokok' => [
                'keywords' => ['alkohol', 'rokok', 'nikotin', 'miras', 'minuman keras', 'ketergantungan', 'zat adiktif', 'kecanduan', 'bahaya rokok', 'bahaya alkohol'],
                'title' => 'Efek Samping & Bahaya Alkohol / Rokok',
                'icon' => 'ri-alert-line',
                'desc' => 'Konsumsi alkohol (minuman keras) dan rokok memiliki efek samping buruk bagi kesehatan organ tubuh, terutama paru-paru, hati (liver), jantung, dan sistem saraf.',
                'symptoms' => [
                    'Jangka Pendek: Penurunan kesadaran, gangguan koordinasi motorik, mual, sakit kepala, dehidrasi, peningkatan tekanan darah secara drastis.',
                    'Jangka Panjang (Alkohol): Kerusakan hati kronis (sirosis), gerd/maag akut, stroke, gangguan memori/kognitif, ketergantungan fisik.',
                    'Jangka Panjang (Rokok): Kanker paru-paru, PPOK (Penyakit Paru Obstruktif Kronis), serangan jantung, gangguan kehamilan.'
                ],
                'prevention' => [
                    'Mengurangi konsumsi secara bertahap dan menetapkan komitmen kuat untuk berhenti.',
                    'Menghindari lingkungan sosial yang memicu keinginan untuk mengonsumsi zat tersebut.',
                    'Berkonsultasi dengan dokter puskesmas untuk terapi konseling berhenti merokok/alkohol.'
                ],
                'treatment' => 'Lakukan gaya hidup sehat, minum banyak air putih untuk detoksifikasi alami tubuh, penuhi asupan nutrisi kaya antioksidan. Jika mengalami gejala putus zat berat (withdrawal) atau keracunan alkohol akut, segera kunjungi layanan medis darurat Puskesmas.'
            ],
            'dbd' => [
                'keywords' => ['dbd', 'demam berdarah', 'dengue', 'aegypti', 'trombosit'],
                'title' => 'Demam Berdarah Dengue (DBD)',
                'icon' => 'ri-virus-line',
                'desc' => 'DBD adalah penyakit infeksi virus dengue yang ditularkan melalui gigitan nyamuk <em>Aedes aegypti</em>.',
                'symptoms' => [
                    'Demam tinggi mendadak (2-7 hari)',
                    'Nyeri di belakang mata, sendi, dan otot',
                    'Bintik-bintik merah di kulit yang tidak pudar saat ditekan',
                    'Mual, muntah, dan tubuh terasa sangat lemas'
                ],
                'prevention' => [
                    'Menerapkan gerakan <strong>3M Plus</strong> (Menguras wadah air, Menutup rapat penampungan air, Mendaur ulang barang bekas).',
                    'Menggunakan obat nyamuk atau kelambu saat tidur.',
                    'Memasang kawat kasa pada jendela dan ventilasi.'
                ],
                'treatment' => 'Banyak minum cairan/air putih, istirahat total, minum parasetamol untuk meredakan demam (hindari aspirin/ibuprofen karena dapat meningkatkan risiko pendarahan), dan segera bawa ke Puskesmas/RS jika trombosit turun atau ada pendarahan.'
            ],
            'tbc' => [
                'keywords' => ['tbc', 'tuberkulosis', 'tuberculosis', 'paru-paru', 'flek paru', 'batuk berdarah'],
                'title' => 'Tuberkulosis (TBC)',
                'icon' => 'ri-lungs-line',
                'desc' => 'TBC adalah penyakit infeksi bakteri <em>Mycobacterium tuberculosis</em> yang menyerang paru-paru dan dapat menular melalui udara saat penderita batuk atau bersin.',
                'symptoms' => [
                    'Batuk berdahak terus-menerus selama lebih dari 2 minggu (bisa disertai darah)',
                    'Demam ringan berkepanjangan terutama di malam hari',
                    'Keringat dingin di malam hari tanpa aktivitas fisik',
                    'Penurunan berat badan secara drastis dan nafsu makan berkurang'
                ],
                'prevention' => [
                    'Mendapatkan imunisasi vaksin <strong>BCG</strong> pada bayi usia 1 bulan.',
                    'Menjaga sirkulasi udara dan cahaya matahari masuk ke dalam rumah.',
                    'Menutup mulut dengan masker/tisu saat batuk dan tidak membuang dahak sembarangan.'
                ],
                'treatment' => 'TBC dapat disembuhkan total dengan terapi kombinasi Obat Anti Tuberkulosis (OAT) secara rutin selama minimal 6 bulan tanpa terputus, di bawah pengawasan dokter dan PMO (Pengawas Menelan Obat).'
            ],
            'diare' => [
                'keywords' => ['diare', 'mencret', 'berak', 'menceret', 'feses cair'],
                'title' => 'Diare',
                'icon' => 'ri-heart-pulse-line',
                'desc' => 'Diare adalah kondisi buang air besar encer atau cair yang terjadi lebih dari 3 kali dalam sehari.',
                'symptoms' => [
                    'Feses lembek/cair',
                    'Kram atau nyeri perut',
                    'Kembung, mual, dan muntah',
                    'Lemas dan rasa haus berlebih (gejala awal dehidrasi)'
                ],
                'prevention' => [
                    'Mencuci tangan dengan sabun dan air mengalir sebelum makan dan setelah dari toilet.',
                    'Mengkonsumsi air minum yang dimasak matang dan menjaga kebersihan makanan.',
                    'Pemberian vaksin rotavirus pada bayi.'
                ],
                'treatment' => 'Minum oralit atau cairan pengganti secukupnya setiap setelah buang air besar untuk mencegah dehidrasi. Untuk anak-anak, berikan suplemen Zinc sesuai petunjuk dokter selama 10 hari berturut-turut.'
            ],
            'polio' => [
                'keywords' => ['polio', 'lumpuh layu', 'lumpuh', 'kelumpuhan'],
                'title' => 'Polio',
                'icon' => 'ri-shield-check-line',
                'desc' => 'Polio adalah penyakit saraf sangat menular yang disebabkan oleh virus polio, berpotensi menyebabkan kelumpuhan permanen hingga kematian.',
                'symptoms' => [
                    'Demam, kelelahan, sakit kepala, dan muntah',
                    'Kekakuan pada leher dan nyeri pada anggota badan',
                    'Kelumpuhan otot secara mendadak pada kasus berat'
                ],
                'prevention' => [
                    'Mendapatkan <strong>Imunisasi Polio lengkap</strong> sejak bayi (polio tetes/OPV dan polio suntik/IPV).'
                ],
                'treatment' => 'Tidak ada obat untuk menyembuhkan polio. Penanganan berfokus pada terapi suportif, fisioterapi untuk melatih otot, dan manajemen kenyamanan pasien.'
            ],
            'campak' => [
                'keywords' => ['campak', 'rubella', 'mr', 'cacar', 'ruam merah'],
                'title' => 'Campak & Rubella',
                'icon' => 'ri-temp-hot-line',
                'desc' => 'Campak adalah infeksi virus sangat menular ditandai ruam kulit kemerahan. Rubella (campak Jerman) memiliki gejala mirip namun sangat berbahaya bagi ibu hamil karena dapat merusak perkembangan janin (sindrom rubella kongenital).',
                'symptoms' => [
                    'Demam tinggi',
                    'Batuk, pilek, dan mata merah (konjungtivitis)',
                    'Ruam merah yang menyebar dari wajah ke seluruh tubuh'
                ],
                'prevention' => [
                    'Dapatkan <strong>vaksinasi MR</strong> secara lengkap pada anak (dosis pertama usia 9 bulan, dosis kedua usia 18 bulan, dan dosis penguat di sekolah dasar).'
                ],
                'treatment' => 'Istirahat cukup, minum banyak air putih, konsumsi pereda demam (parasetamol), dan pemberian Vitamin A dosis tinggi sesuai anjuran tenaga medis.'
            ],
            'sakit_hati' => [
                'keywords' => ['sakit hati', 'hati', 'liver', 'hepar', 'hepatitis', 'sirosis', 'kuning'],
                'title' => 'Gangguan Organ Hati (Sakit Hati Medis) & Hepatitis',
                'icon' => 'ri-contrast-drop-2-line',
                'desc' => 'Secara medis, "sakit hati" merujuk pada gangguan organ hati (liver/hepar), seperti hepatitis, perlemakan hati (fatty liver), atau sirosis. Namun secara psikologis, istilah ini sering merujuk pada tekanan emosional/stres.',
                'symptoms' => [
                    'Kulit dan bagian putih mata menguning (jaundice)',
                    'Urin berwarna gelap seperti teh',
                    'Mual, muntah, cepat lelah, dan nyeri di perut kanan atas',
                    'Jika psikologis: perasaan sedih, cemas, sesak di dada, susah fokus'
                ],
                'prevention' => [
                    'Melakukan vaksinasi Hepatitis B secara lengkap.',
                    'Menghindari konsumsi alkohol dan membatasi makanan berlemak tinggi.',
                    'Menghindari penggunaan obat-obatan jangka panjang tanpa resep dokter.',
                    'Jika psikologis: lakukan teknik relaksasi, meditasi, dan batasi stres.'
                ],
                'treatment' => 'Secara medis, lakukan istirahat cukup, hindari alkohol/makanan berlemak, dan konsultasikan dengan dokter spesialis. Jika sakit hati merujuk pada aspek emosional (stres/patah hati/kesedihan), lakukan manajemen stres, meditasi, istirahat cukup, dan bicarakan keluhan dengan orang terdekat atau profesional kesehatan mental.'
            ],
            'sakit_kepala' => [
                'keywords' => ['pusing', 'sakit kepala', 'migrain', 'vertigo', 'kepala'],
                'title' => 'Sakit Kepala, Migrain & Vertigo',
                'icon' => 'ri-mind-map',
                'desc' => 'Sakit kepala adalah rasa nyeri di area kepala yang bisa berupa sakit kepala tegang (tension headache), migrain (sakit kepala sebelah), atau vertigo (sensasi sekeliling berputar).',
                'symptoms' => [
                    'Nyeri berdenyut atau kaku di sekitar kepala/leher',
                    'Sensasi berputar (pada vertigo)',
                    'Sensitivitas terhadap cahaya dan suara (pada migrain)'
                ],
                'prevention' => [
                    'Menjaga pola tidur yang teratur dan cukup.',
                    'Mengelola stres dengan baik dan menghindari kelelahan fisik.',
                    'Menghindari perubahan posisi kepala secara mendadak (pada vertigo).'
                ],
                'treatment' => 'Istirahat di ruangan yang tenang dan gelap, cukupi cairan tubuh, minum parasetamol atau ibuprofen untuk nyeri ringan, dan lakukan manuver rehabilitasi jika disarankan dokter untuk vertigo.'
            ],
            'lambung' => [
                'keywords' => ['lambung', 'maag', 'gerd', 'asam lambung', 'perih', 'ulu hati'],
                'title' => 'Gangguan Asam Lambung (Maag & GERD)',
                'icon' => 'ri-heart-pulse-fill',
                'desc' => 'Gangguan lambung umumnya disebabkan oleh peningkatan asam lambung atau iritasi pada dinding lambung (gastritis/maag), serta aliran balik asam lambung ke kerongkongan (GERD).',
                'symptoms' => [
                    'Nyeri ulu hati atau perih di lambung',
                    'Perut kembung dan sering bersendawa',
                    'Mual dan muntah',
                    'Sensasi terbakar di dada (heartburn) pada penderita GERD'
                ],
                'prevention' => [
                    'Makan secara teratur dengan porsi kecil tapi sering.',
                    'Hindari langsung berbaring setelah makan (tunggu minimal 2 jam).',
                    'Batasi makanan pedas, asam, bersantan, kopi, dan cokelat.'
                ],
                'treatment' => 'Konsumsi antasida atau obat penurun asam lambung sesuai petunjuk dokter untuk meredakan gejala akut lambung perih.'
            ],
            'demam' => [
                'keywords' => ['demam', 'panas', 'menggigil'],
                'title' => 'Demam (Fever)',
                'icon' => 'ri-temp-hot-line',
                'desc' => 'Demam adalah kondisi ketika suhu tubuh naik di atas 38°C, yang merupakan respon alami sistem pertahanan tubuh untuk melawan infeksi virus atau bakteri.',
                'symptoms' => [
                    'Suhu tubuh melebihi 37.5°C atau 38°C',
                    'Menggigil atau berkeringat dingin',
                    'Sakit kepala dan lemas'
                ],
                'prevention' => [
                    'Menjaga daya tahan tubuh tetap prima.',
                    'Melakukan vaksinasi secara rutin.',
                    'Menghindari kontak langsung dengan orang yang sedang sakit infeksi.'
                ],
                'treatment' => 'Kompres hangat (bukan air dingin), minum air putih lebih banyak, gunakan pakaian tipis, dan konsumsi parasetamol sesuai dosis.'
            ],
            'batuk_pilek' => [
                'keywords' => ['batuk', 'pilek', 'flu', 'influenza', 'bersin', 'tenggorokan'],
                'title' => 'Batuk, Pilek & Influenza',
                'icon' => 'ri-windy-line',
                'desc' => 'Batuk dan pilek umumnya merupakan gejala infeksi saluran pernapasan atas (ISPA) yang sering kali disebabkan oleh infeksi virus ringan (common cold) atau virus influenza.',
                'symptoms' => [
                    'Hidung tersumbat atau berair',
                    'Tenggorokan gatal atau sakit',
                    'Batuk kering atau berdahak',
                    'Bersin-bersin'
                ],
                'prevention' => [
                    'Mencuci tangan secara rutin.',
                    'Menggunakan masker di tempat umum dan menerapkan etika batuk yang benar.',
                    'Menjaga daya tahan tubuh dengan pola hidup sehat.'
                ],
                'treatment' => 'Istirahat cukup, konsumsi cairan hangat (air hangat, teh herbal, sup), konsumsi madu hangat (untuk usia >1 tahun), dan gunakan obat pereda batuk/flu jika diperlukan.'
            ],
            'diabetes' => [
                'keywords' => ['diabetes', 'gula darah', 'kencing manis', 'insulin'],
                'title' => 'Diabetes Melitus (Kencing Manis)',
                'icon' => 'ri-contrast-drop-line',
                'desc' => 'Diabetes melitus adalah penyakit metabolik kronis yang ditandai dengan peningkatan kadar gula darah di atas batas normal akibat tubuh tidak menghasilkan cukup insulin atau tidak merespon insulin dengan baik.',
                'symptoms' => [
                    'Sering merasa lapar secara berlebih (Polifagia)',
                    'Sering merasa haus terus-menerus (Polidipsia)',
                    'Sering buang air kecil, terutama di malam hari (Poliuria)',
                    'Penurunan berat badan tanpa sebab yang jelas'
                ],
                'prevention' => [
                    'Membatasi konsumsi gula sederhana, makanan manis, dan karbohidrat olahan.',
                    'Rutin melakukan aktivitas fisik atau olahraga minimal 30 menit sehari.',
                    'Menjaga berat badan ideal.'
                ],
                'treatment' => 'Rutin memeriksa kadar gula darah, mengikuti terapi obat penurun gula darah atau insulin sesuai resep dokter, dan menjaga pola makan rendah indeks glikemik.'
            ],
            'hipertensi' => [
                'keywords' => ['hipertensi', 'darah tinggi', 'tensi tinggi', 'tekanan darah'],
                'title' => 'Hipertensi (Tekanan Darah Tinggi)',
                'icon' => 'ri-pulse-line',
                'desc' => 'Hipertensi adalah kondisi kronis ketika tekanan darah pada dinding arteri berada di angka 140/90 mmHg atau lebih tinggi secara konsisten. Sering dijuluki sebagai "silent killer" karena kerap tanpa gejala.',
                'symptoms' => [
                    'Sakit kepala atau pusing (terutama di bagian belakang kepala)',
                    'Kelelahan atau kebingungan',
                    'Nyeri dada atau jantung berdebar',
                    'Masalah penglihatan pada kasus yang sangat tinggi'
                ],
                'prevention' => [
                    'Kurangi konsumsi garam harian (maksimal 1 sendok teh atau 5 gram sehari).',
                    'Rutin berolahraga dan lakukan manajemen stres secara sehat.',
                    'Hindari asap rokok dan konsumsi alkohol.'
                ],
                'treatment' => 'Periksa tekanan darah secara berkala, lakukan diet DASH (rendah garam, tinggi serat), dan minum obat pengontrol tekanan darah secara teratur sesuai resep dokter.'
            ],
            'kolesterol' => [
                'keywords' => ['kolesterol', 'lemak darah'],
                'title' => 'Kolesterol Tinggi',
                'icon' => 'ri-heart-line',
                'desc' => 'Kondisi ketika kadar kolesterol total dalam darah melebihi 200 mg/dL, yang dapat memicu penumpukan plak pada dinding pembuluh darah (aterosklerosis) dan menyumbat aliran darah ke jantung atau otak.',
                'symptoms' => [
                    'Umumnya tidak bergejala, namun kadar kolesterol tinggi yang lama dapat memicu nyeri tengkuk/pundak kaku atau nyeri dada (angina).'
                ],
                'prevention' => [
                    'Mengkonsumsi makanan tinggi serat seperti buah-buahan, sayuran, dan gandum utuh.',
                    'Memilih lemak sehat (minyak zaitun, alpukat) dan mengurangi gorengan.',
                    'Melakukan olahraga aerobik secara teratur.'
                ],
                'treatment' => 'Jaga pola makan rendah lemak jenuh, rutin olahraga, dan konsultasikan dengan dokter mengenai perlunya obat penurun kolesterol (seperti golongan statin).'
            ],
            'asma' => [
                'keywords' => ['asma', 'sesak napas', 'sesak', 'mengi', 'ngik'],
                'title' => 'Asma & Gangguan Pernapasan',
                'icon' => 'ri-lungs-fill',
                'desc' => 'Asma adalah penyakit kronis pada saluran pernapasan yang ditandai dengan peradangan dan penyempitan saluran napas, menyebabkan penderita mengalami sesak napas, mengi (bunyi ngik-ngik), dan batuk.',
                'symptoms' => [
                    'Sesak napas atau dada terasa terikat',
                    'Mengi (bunyi napas ngik-ngik)',
                    'Batuk kronis terutama di malam hari atau saat udara dingin'
                ],
                'prevention' => [
                    'Kenali dan hindari faktor pemicu serangan asma (debu, dingin, asap rokok, bulu hewan).',
                    'Jaga kebersihan lingkungan tidur dari tungau debu.'
                ],
                'treatment' => 'Gunakan inhaler bronkodilator pelega napas saat serangan terjadi sesuai petunjuk dokter, dan gunakan obat pengontrol jangka panjang jika diresepkan.'
            ],
            'gigi' => [
                'keywords' => ['gigi', 'gusi', 'karies', 'sakit gigi'],
                'title' => 'Sakit Gigi & Masalah Mulut',
                'icon' => 'ri-shield-user-line',
                'desc' => 'Sakit gigi umumnya terjadi akibat kerusakan lapisan gigi luar (karies) yang mencapai saraf gigi, sementara gusi bengkak/berdarah biasanya disebabkan oleh radang gusi (gingivitis).',
                'symptoms' => [
                    'Nyeri tajam, berdenyut, atau konstan pada gigi',
                    'Gusi bengkak, merah, atau mudah berdarah',
                    'Sensitivitas gigi terhadap makanan/minuman panas atau dingin'
                ],
                'prevention' => [
                    'Menyikat gigi secara teratur minimal 2 kali sehari menggunakan pasta gigi berfluoride.',
                    'Mengurangi konsumsi makanan manis dan lengket.',
                    'Rutin periksa ke dokter gigi minimal 6 bulan sekali.'
                ],
                'treatment' => 'Berkumur air garam hangat, gunakan obat pereda nyeri parasetamol/ibuprofen sementara waktu, dan segera periksakan ke dokter gigi untuk penanganan permanen.'
            ],
            'stres' => [
                'keywords' => ['stress', 'stres', 'cemas', 'anxiety', 'depresi', 'mental', 'jiwa', 'patah hati'],
                'title' => 'Stres, Kecemasan & Kesehatan Mental',
                'icon' => 'ri-user-heart-fill',
                'desc' => 'Stres dan cemas adalah respon psikologis dan fisik alami tubuh terhadap tuntutan atau tekanan. Namun, jika terjadi berkepanjangan dapat mengganggu kesehatan fisik dan mental secara serius.',
                'symptoms' => [
                    'Perasaan tegang, gelisah, atau khawatir berlebih',
                    'Gangguan tidur atau kelelahan konstan',
                    'Gejala fisik seperti jantung berdebar, sakit perut, atau otot tegang'
                ],
                'prevention' => [
                    'Melakukan teknik relaksasi secara rutin (pernapasan dalam, meditasi, yoga).',
                    'Mengatur keseimbangan antara waktu kerja, istirahat, dan rekreasi.',
                    'Membatasi kafein dan alkohol.'
                ],
                'treatment' => 'Terapkan manajemen stres secara sehat, rutin berolahraga, dan bicarakan keluhan dengan orang terdekat atau profesional kesehatan mental (psikolog/psikiater) jika mengganggu aktivitas harian.'
            ],
            'insomnia' => [
                'keywords' => ['insomnia', 'tidur', 'begadang', 'kurang tidur'],
                'title' => 'Gangguan Tidur (Insomnia)',
                'icon' => 'ri-moon-clear-line',
                'desc' => 'Insomnia adalah gangguan tidur yang menyebabkan seseorang sulit tidur, sering terbangun di malam hari dan sulit tidur kembali, sehingga merasa lelah saat bangun.',
                'symptoms' => [
                    'Kesulitan untuk memulai tidur di malam hari',
                    'Terbangun di malam hari dan tidak bisa tidur kembali',
                    'Merasa lelah, mengantuk, atau sulit konsentrasi di siang hari'
                ],
                'prevention' => [
                    'Buat jadwal tidur dan bangun yang konsisten setiap hari.',
                    'Matikan layar gawai (HP/laptop) minimal 1 jam sebelum tidur.',
                    'Buat kamar tidur tenang, gelap, dan sejuk.'
                ],
                'treatment' => 'Terapkan sleep hygiene (kebiasaan tidur sehat), hindari kafein/makan berat menjelang tidur, lakukan aktivitas relaksasi sebelum tidur, dan konsultasikan ke dokter jika insomnia berlangsung kronis.'
            ],
            'imunisasi' => [
                'keywords' => ['imunisasi', 'vaksin', 'vaksinasi', 'kipi', 'suntik imunisasi'],
                'title' => 'Imunisasi, Vaksinasi & KIPI',
                'icon' => 'ri-notification-badge-line',
                'desc' => 'Imunisasi adalah upaya untuk membentuk kekebalan tubuh secara aktif terhadap suatu penyakit menular lewat pemberian vaksin, sehingga melindungi individu dan populasi dari wabah berbahaya.',
                'symptoms' => [
                    'Reaksi KIPI (Kejadian Ikutan Pasca Imunisasi) yang umum meliputi demam ringan/sedang, kemerahan, bengkak, atau nyeri di area suntikan, serta anak menjadi rewel/lemas.'
                ],
                'prevention' => [
                    'Mematuhi jadwal imunisasi dasar lengkap anak dari Kementerian Kesehatan RI.',
                    'Menjaga kondisi anak tetap sehat dan bugar sebelum jadwal vaksinasi.'
                ],
                'treatment' => 'Kompres air hangat pada bekas suntikan, berikan obat penurun panas (parasetamol) sesuai dosis jika demam, beri ASI/air putih lebih sering, dan kenakan pakaian tipis yang nyaman. Jika gejala KIPI sangat berat, segera hubungi fasilitas kesehatan terdekat.'
            ],
            'kulit' => [
                'keywords' => ['kulit', 'gatal', 'alergi', 'ruam', 'bintik', 'kadas', 'kurap', 'panu', 'eksim', 'gatal-gatal'],
                'title' => 'Kesehatan Kulit, Alergi & Gatal-gatal',
                'icon' => 'ri-shield-star-line',
                'desc' => 'Masalah kulit seperti gatal, ruam, dan alergi bisa dipicu oleh infeksi bakteri/jamur, reaksi alergi terhadap makanan atau lingkungan, eksim, maupun gigitan serangga.',
                'symptoms' => [
                    'Rasa gatal yang memicu keinginan menggaruk',
                    'Bercak kemerahan, bintil-bintil, atau kulit bersisik',
                    'Kulit kering, pecah-pecah, atau timbul lepuhan kecil'
                ],
                'prevention' => [
                    'Menjaga kebersihan tubuh dengan mandi teratur menggunakan sabun lembut.',
                    'Menghindari bahan pemicu alergi (alergen) yang sudah diketahui.',
                    'Tidak berbagi handuk atau pakaian pribadi dengan orang lain.'
                ],
                'treatment' => 'Gunakan bedak kocok kalamin atau lotion pelembap untuk meredakan gatal sementara, hindari menggaruk kulit agar tidak infeksi sekunder, dan gunakan obat antihistamin sesuai resep dokter jika dipicu alergi.'
            ],
            'jantung' => [
                'keywords' => ['jantung', 'nyeri dada', 'dada', 'sesak dada', 'koroner'],
                'title' => 'Kesehatan Jantung & Nyeri Dada',
                'icon' => 'ri-heart-pulse-fill',
                'desc' => 'Penyakit jantung (seperti penyakit jantung koroner) terjadi akibat penyempitan pembuluh darah utama yang menyuplai darah ke jantung. Nyeri dada hebat (angina) adalah salah satu alarm utamanya.',
                'symptoms' => [
                    'Nyeri dada kiri seperti ditekan, diremas, atau tertindih benda berat',
                    'Nyeri yang menjalar ke bahu, lengan kiri, leher, atau rahang',
                    'Disertai sesak napas, keringat dingin, mual, atau pusing berputar'
                ],
                'prevention' => [
                    'Menerapkan pola makan rendah lemak jenuh dan tinggi serat.',
                    'Rutin berolahraga secara teratur minimal 150 menit per minggu.',
                    'Mengontrol berat badan, tekanan darah, dan kadar gula darah.'
                ],
                'treatment' => '<strong>PENTING:</strong> Nyeri dada kiri hebat yang menjalar dan disertai keringat dingin adalah kondisi darurat medis. Segera bawa ke IGD rumah sakit terdekat. Untuk pencegahan jangka panjang, konsumsilah obat pengontrol jantung sesuai resep dokter spesialis jantung.'
            ],
            'kanker' => [
                'keywords' => ['kanker', 'tumor', 'benjolan', 'karsinoma'],
                'title' => 'Edukasi Kanker & Deteksi Dini',
                'icon' => 'ri-alarm-warning-line',
                'desc' => 'Kanker adalah pertumbuhan sel abnormal yang tidak terkendali di dalam tubuh yang dapat merusak jaringan sehat di sekitarnya.',
                'symptoms' => [
                    'Adanya benjolan yang tidak biasa dan terus membesar',
                    'Perubahan pada kulit atau tahi lalat yang mencurigakan',
                    'Penurunan berat badan drastis tanpa sebab jelas',
                    'Pendarahan atau keluarnya cairan tidak normal dari tubuh'
                ],
                'prevention' => [
                    'Menerapkan gaya hidup sehat dengan gizi seimbang.',
                    'Menghindari paparan asap rokok dan karsinogen lainnya.',
                    'Rutin melakukan pemeriksaan skrining dini (seperti SADARI untuk kanker payudara atau IVA test/Pap smear untuk kanker serviks).'
                ],
                'treatment' => 'Penanganan kanker melibatkan biopsi, pembedahan, kemoterapi, radioterapi, atau imunoterapi yang disesuaikan dengan stadium dan jenis kanker oleh tim dokter onkologi.'
            ],
            'luka' => [
                'keywords' => ['luka', 'pendarahan', 'baret', 'lecet', 'luka bakar', 'tergores'],
                'title' => 'Pertolongan Pertama pada Luka',
                'icon' => 'ri-shield-cross-line',
                'desc' => 'Luka adalah kerusakan pada jaringan tubuh (biasanya kulit) akibat trauma fisik, seperti tergores, terjatuh, atau terbakar.',
                'symptoms' => [
                    'Terputusnya kontinuitas kulit, perdarahan, nyeri di area cedera, serta bengkak ringan.'
                ],
                'prevention' => [
                    'Menggunakan alat pelindung diri yang sesuai saat beraktivitas berisiko.',
                    'Menjaga lingkungan rumah dan bermain aman dari benda tajam.'
                ],
                'treatment' => 'Bersihkan luka dengan air mengalir untuk menghilangkan kotoran, berikan antiseptik (seperti povidone iodine), tutup dengan plester atau kassa steril. Untuk luka bakar ringan, dinginkan segera di bawah air mengalir (bukan es atau odol) selama 10-15 menit.'
            ],
            'diet' => [
                'keywords' => ['diet', 'berat badan', 'gizi', 'kalori', 'obesitas', 'makanan sehat', 'nutrisi'],
                'title' => 'Nutrisi, Diet Sehat & Pengelolaan Berat Badan',
                'icon' => 'ri-restaurant-line',
                'desc' => 'Pola makan bergizi seimbang adalah kunci utama menjaga kesehatan tubuh, berat badan ideal, dan mencegah berbagai penyakit kronis.',
                'symptoms' => [
                    'Ketidakseimbangan nutrisi dapat memicu kondisi kelebihan berat badan (obesitas) atau kekurangan gizi (malnutrisi/stunting).'
                ],
                'prevention' => [
                    'Terapkan pola makan "Isi Piringku" (50% buah dan sayur, 50% karbohidrat dan lauk pauk).',
                    'Batasi konsumsi GGL (Gula maks 4 sendok makan, Garam maks 1 sendok teh, Lemak maks 5 sendok makan per hari).'
                ],
                'treatment' => 'Jalani diet sehat terencana dengan defisit kalori sehat bila obesitas, rutin olahraga, penuhi cairan tubuh, dan konsultasikan ke dokter gizi atau nutrisionis bila membutuhkan panduan khusus.'
            ]
        ];

        // 1. Scoring matched topics based on keyword occurrences
        $matchedTopics = [];
        $thinkingSteps = [
            "Menerima pertanyaan pengguna: \"" . e($message) . "\"",
            "Menganalisis pesan untuk mendeteksi intensi dan topik kesehatan utama..."
        ];

        foreach ($topics as $key => $data) {
            $score = 0;
            foreach ($data['keywords'] as $keyword) {
                if (str_contains($lowercaseMessage, $keyword)) {
                    $score++;
                }
            }
            if ($score > 0) {
                $matchedTopics[$key] = $score;
            }
        }

        // Sort matched topics by score descending
        arsort($matchedTopics);
        $topTopicKeys = array_keys($matchedTopics);

        // 2. Intent Parsing (Gejala, Pencegahan, Pengobatan, Penyebab)
        $intents = [
            'gejala' => ['gejala', 'tanda', 'ciri', 'indikasi', 'merasakan', 'rasanya', 'terasa'],
            'pencegahan' => ['cegah', 'pencegahan', 'menghindari', 'proteksi', 'vaksin', 'imunisasi', 'kebutuhan imun', 'menjaga'],
            'pengobatan' => ['obat', 'mengobati', 'penanganan', 'meredakan', 'terapi', 'sembuh', 'penyembuhan', 'pertolongan pertama', 'atasi', 'mengatasi'],
            'penyebab' => ['sebab', 'penyebab', 'disebabkan', 'karena', 'pemicu', 'asal']
        ];

        $matchedIntents = [];
        foreach ($intents as $intentKey => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lowercaseMessage, $keyword)) {
                    $matchedIntents[] = $intentKey;
                    break;
                }
            }
        }

        $primaryIntent = count($matchedIntents) > 0 ? $matchedIntents[0] : 'umum';

        // 3. Dynamic Response Building & Synthesis
        $reply = '';

        if (count($matchedTopics) === 0) {
            // General health fallback
            $thinkingSteps[] = "Hasil Analisis: Tidak mendeteksi topik penyakit spesifik, mendeteksi keluhan/pertanyaan kesehatan umum.";
            $thinkingSteps[] = "Menyusun rekomendasi gaya hidup sehat dan langkah imunitas dasar.";

            $reply = '<h3><i class="ri-customer-service-2-line"></i> Rekomendasi & Edukasi Kesehatan Umum</h3>' .
                     '<p>Terima kasih atas pertanyaan Anda mengenai kesehatan. Berikut adalah beberapa langkah edukasi kesehatan umum yang bisa diterapkan:</p>' .
                     '<ul>' .
                     '<li><strong>Asupan Nutrisi & Hidrasi:</strong> Konsumsi makanan dengan gizi seimbang dan penuhi kebutuhan cairan harian minimal 2 liter air putih per hari.</li>' .
                     '<li><strong>Gaya Hidup Bersih:</strong> Rutin mencuci tangan menggunakan sabun, menjaga kebersihan alat makan, dan mengelola sampah rumah tangga dengan baik.</li>' .
                     '<li><strong>Istirahat & Imunitas:</strong> Pastikan waktu tidur cukup (7-8 jam untuk dewasa) guna mendukung sistem kekebalan tubuh melawan infeksi secara optimal.</li>' .
                     '<li><strong>Pencegahan Mandiri:</strong> Selalu gunakan masker bila sedang batuk/pilek untuk mencegah penularan ke keluarga terdekat.</li>' .
                     '</ul>' .
                     '<p><em>Catatan Penting: Informasi di atas adalah bagian dari edukasi kesehatan dasar. Jika keluhan Anda berlanjut lebih dari 3 hari, terasa memburuk, atau timbul kondisi darurat, mohon segera kunjungi dokter di Puskesmas atau rumah sakit terdekat untuk mendapatkan pemeriksaan langsung.</em></p>' .
                     '<div style="margin-top: 15px; padding: 12px; border-top: 1px dashed rgba(239, 68, 68, 0.3); font-size: 12px; color: var(--danger); background-color: rgba(239, 68, 68, 0.03); border-radius: 8px;">' .
                     '<i class="ri-information-line" style="vertical-align: middle; margin-right: 4px;"></i> <strong>Batasan Mode Terbatas (Lokal/Offline):</strong> Karena sistem saat ini beroperasi dalam Mode Terbatas (Offline), AI tidak dapat memproses jawaban spesifik untuk pertanyaan di luar daftar penyakit utama bawaan (DBD, TBC, Diare, Polio, Campak, Alkohol/Rokok). Hubungi administrator untuk mengaktifkan <strong>Mode Cerdas (Gemini API)</strong> agar AI dapat menjawab semua pertanyaan kesehatan secara dinamis.' .
                     '</div>';
        } elseif (count($matchedTopics) >= 2 && in_array('demam', $topTopicKeys) && in_array('batuk_pilek', $topTopicKeys)) {
            // Sintesis Gejala Demam + Batuk & Pilek
            $thinkingSteps[] = "Mendeteksi beberapa gejala: Demam dan Batuk/Pilek.";
            $thinkingSteps[] = "Hasil Analisis: Gejala ini umumnya mengarah ke Infeksi Saluran Pernapasan Akut (ISPA) atau Influenza.";
            $thinkingSteps[] = "Menyusun respons terpadu (sintesis) untuk kedua gejala tersebut dengan fokus '" . $primaryIntent . "'.";

            $reply = '<h3><i class="ri-pulse-line"></i> Sintesis Gejala: Demam disertai Batuk & Pilek (Kemungkinan ISPA/Flu)</h3>' .
                     '<p><strong>Analisis Medis:</strong> Kombinasi demam yang dibarengi dengan batuk, pilek, bersin, atau tenggorokan gatal merupakan gejala klasik dari <strong>Infeksi Saluran Pernapasan Akut (ISPA)</strong> atau <strong>Influenza (Flu)</strong>. Ini merupakan respon sistem imun dalam melawan infeksi virus di saluran napas.</p>';

            if ($primaryIntent === 'gejala') {
                $reply .= '<div style="background-color: #fffbeb; border: 1px solid #fde68a; padding: 14px; border-radius: 12px; margin-bottom: 15px;">' .
                          '<strong><i class="ri-information-line"></i> Fokus Gejala Utama:</strong>' .
                          '<ul style="margin: 8px 0 0 20px;">' .
                          '<li>Demam (suhu > 37.5°C) disertai menggigil.</li>' .
                          '<li>Hidung mampet, bersin-bersin, dan ingus encer/kental.</li>' .
                          '<li>Batuk kering atau berdahak yang membuat tenggorokan gatal.</li>' .
                          '<li>Sakit kepala ringan, nyeri sendi, dan tubuh terasa pegal-pegal.</li>' .
                          '</ul>' .
                          '</div>';
            } elseif ($primaryIntent === 'pencegahan') {
                $reply .= '<div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 14px; border-radius: 12px; margin-bottom: 15px;">' .
                          '<strong><i class="ri-shield-line"></i> Fokus Langkah Pencegahan Penularan:</strong>' .
                          '<ul style="margin: 8px 0 0 20px;">' .
                          '<li>Gunakan masker untuk mencegah penularan droplet saat batuk/bersin.</li>' .
                          '<li>Terapkan etika batuk (tutup mulut dengan tisu atau lipatan siku).</li>' .
                          '<li>Mencuci tangan dengan sabun setelah bersin atau menyentuh wajah.</li>' .
                          '<li>Hindari kontak fisik dekat dengan anak-anak atau lansia di rumah.</li>' .
                          '</ul>' .
                          '</div>';
            } elseif ($primaryIntent === 'pengobatan') {
                $reply .= '<div style="background-color: #f0f9ff; border: 1px solid #bae6fd; padding: 14px; border-radius: 12px; margin-bottom: 15px;">' .
                          '<strong><i class="ri-heart-line"></i> Fokus Penanganan Pertama & Obat:</strong>' .
                          '<ul style="margin: 8px 0 0 20px;">' .
                          '<li>Konsumsi obat pereda demam dan nyeri seperti Parasetamol sesuai aturan pakai.</li>' .
                          '<li>Minum air putih hangat, air lemon hangat, atau sup untuk mengencerkan dahak.</li>' .
                          '<li>Lakukan istirahat total (minimal 7-8 jam) dan hindari begadang.</li>' .
                          '<li>Gunakan obat batuk/flu bebas yang sesuai jika gejala sangat mengganggu.</li>' .
                          '</ul>' .
                          '</div>';
            } else {
                $reply .= '<p><strong>Rekomendasi Penanganan Mandiri:</strong></p>' .
                          '<ul>' .
                          '<li><strong>Obat & Pereda Demam:</strong> Konsumsi Parasetamol untuk menurunkan demam dan mengurangi sakit kepala/pegal.</li>' .
                          '<li><strong>Hidrasi:</strong> Perbanyak minum air hangat atau sup hangat untuk melembapkan tenggorokan.</li>' .
                          '<li><strong>Istirahat & Masker:</strong> Istirahat total dan gunakan masker agar tidak menularkan ke keluarga lain.</li>' .
                          '</ul>';
            }

            $reply .= '<p><em>Peringatan: Jika demam berlangsung lebih dari 3 hari tanpa penurunan, atau disertai sesak napas yang berbunyi (mengi), segera periksakan diri ke Puskesmas terdekat.</em></p>';

        } else {
            // Single dominant topic response with dynamic highlights based on intent
            $dominantKey = $topTopicKeys[0];
            $topic = $topics[$dominantKey];

            $thinkingSteps[] = "Mendeteksi topik kesehatan utama: " . $topic['title'];
            $thinkingSteps[] = "Mengekstrak fokus pertanyaan (intent): " . strtoupper($primaryIntent);
            $thinkingSteps[] = "Menyusun respon medis terstruktur mengenai " . $topic['title'] . " dengan sorotan pada bagian '" . $primaryIntent . "'.";

            $reply .= '<h3><i class="' . $topic['icon'] . '"></i> ' . $topic['title'] . '</h3>' .
                      '<p><strong>Deskripsi:</strong> ' . $topic['desc'] . '</p>';

            // We render sections with customized order or styling based on intent
            $symptomsHtml = '<div class="symptoms-section ' . ($primaryIntent === 'gejala' ? 'intent-focused-amber' : '') . '" style="margin-bottom: 12px; padding: ' . ($primaryIntent === 'gejala' ? '12px 14px' : '0') . '; border: ' . ($primaryIntent === 'gejala' ? '1px solid #fde68a; background-color: #fffbeb; border-radius: 10px;' : 'none') . ';">' .
                            '<p><strong><i class="ri-shield-user-line"></i> Gejala Utama ' . ($primaryIntent === 'gejala' ? '(Fokus Pertanyaan Anda)' : '') . ':</strong></p>' .
                            '<ul style="margin-left: 20px;">';
            foreach ($topic['symptoms'] as $symptom) {
                $symptomsHtml .= '<li>' . $symptom . '</li>';
            }
            $symptomsHtml .= '</ul></div>';

            $preventionHtml = '<div class="prevention-section ' . ($primaryIntent === 'pencegahan' ? 'intent-focused-green' : '') . '" style="margin-bottom: 12px; padding: ' . ($primaryIntent === 'pencegahan' ? '12px 14px' : '0') . '; border: ' . ($primaryIntent === 'pencegahan' ? '1px solid #bbf7d0; background-color: #f0fdf4; border-radius: 10px;' : 'none') . ';">' .
                              '<p><strong><i class="ri-shield-line"></i> Langkah Pencegahan ' . ($primaryIntent === 'pencegahan' ? '(Fokus Pertanyaan Anda)' : '') . ':</strong></p>' .
                              '<ul style="margin-left: 20px;">';
            foreach ($topic['prevention'] as $prev) {
                $preventionHtml .= '<li>' . $prev . '</li>';
            }
            $preventionHtml .= '</ul></div>';

            $treatmentHtml = '<div class="treatment-section ' . ($primaryIntent === 'pengobatan' ? 'intent-focused-blue' : '') . '" style="margin-bottom: 12px; padding: ' . ($primaryIntent === 'pengobatan' ? '12px 14px' : '0') . '; border: ' . ($primaryIntent === 'pengobatan' ? '1px solid #bae6fd; background-color: #f0f9ff; border-radius: 10px;' : 'none') . ';">' .
                             '<p><strong><i class="ri-heart-line"></i> Rekomendasi Penanganan & Obat ' . ($primaryIntent === 'pengobatan' ? '(Fokus Pertanyaan Anda)' : '') . ':</strong></p>' .
                             '<p style="margin-left: 4px;">' . $topic['treatment'] . '</p></div>';

            // Output logic order based on primary intent
            if ($primaryIntent === 'gejala') {
                $reply .= $symptomsHtml . $preventionHtml . $treatmentHtml;
            } elseif ($primaryIntent === 'pencegahan') {
                $reply .= $preventionHtml . $symptomsHtml . $treatmentHtml;
            } elseif ($primaryIntent === 'pengobatan') {
                $reply .= $treatmentHtml . $symptomsHtml . $preventionHtml;
            } else {
                $reply .= $symptomsHtml . $preventionHtml . $treatmentHtml;
            }
        }

        return response()->json([
            'thinking' => $thinkingSteps,
            'reply' => $reply
        ]);
    }
}
