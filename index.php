<?php
// ==========================================
// Reels Prank Web App - Yapılandırma Ayarları
// ==========================================
$config = [
    'default_name' => 'Dostum', // URL'de ?isim= parametresi yoksa kullanılacak varsayılan isim
    'video_url' => 'https://assets.mixkit.co/videos/preview/mixkit-cute-cat-looking-at-camera-31154-large.mp4', // Reels dikey videosu
    'video_delay' => 7000, // Glitch ve sızma simülasyonu başlamadan önce videonun oynatılacağı süre (milisaniye)
    'hacker_group' => 'ANONYMOUS_JOKER', // Terminalde görüntülenecek sahte grup adı
];

// Ziyaretçi Bilgilerini Alma (Zararsız şaka simülasyonunda kullanılacaktır)
$user_ip = $_SERVER['REMOTE_ADDR'];
// Yerel sunucu testi için inandırıcı bir IP simülasyonu yapalım
if ($user_ip === '::1' || $user_ip === '127.0.0.1') {
    $user_ip = '192.168.1.108'; 
}

$user_agent = $_SERVER['HTTP_USER_AGENT'];
$os = "Bilinmeyen İS";
$browser = "Bilinmeyen Tarayıcı";

// Basit İşletim Sistemi Analizi
if (preg_match('/mac/i', $user_agent)) {
    $os = "macOS";
} elseif (preg_match('/windows|win32/i', $user_agent)) {
    $os = "Windows";
} elseif (preg_match('/iphone|ipad/i', $user_agent)) {
    $os = "iOS";
} elseif (preg_match('/android/i', $user_agent)) {
    $os = "Android";
} elseif (preg_match('/linux/i', $user_agent)) {
    $os = "Linux";
}

// Basit Tarayıcı Analizi
if (preg_match('/chrome/i', $user_agent)) {
    $browser = "Google Chrome";
} elseif (preg_match('/safari/i', $user_agent) && !preg_match('/chrome/i', $user_agent)) {
    $browser = "Safari";
} elseif (preg_match('/firefox/i', $user_agent)) {
    $browser = "Mozilla Firefox";
} elseif (preg_match('/edge/i', $user_agent)) {
    $browser = "Microsoft Edge";
} elseif (preg_match('/opera/i', $user_agent)) {
    $browser = "Opera";
}

// Şaka hedefini kişiselleştirmek için URL parametresini kontrol et
$target_name = isset($_GET['isim']) ? htmlspecialchars($_GET['isim']) : $config['default_name'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Instagram Reels</title>
    <!-- Google Fonts: Inter & JetBrains Mono (Terminal için) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --ig-primary-gradient: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            --terminal-green: #39ff14;
            --terminal-glow: rgba(57, 255, 20, 0.4);
            --danger-red: #ff3040;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-user-select: none;
            user-select: none;
        }

        body {
            background-color: #000;
            color: #fff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Dış Çerçeve (Desktop için Telefon Görünümü) */
        .app-container {
            position: relative;
            width: 100%;
            height: 100vh;
            max-width: 450px;
            max-height: 850px;
            background: #000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
            border-radius: 0;
            transition: all 0.3s ease;
        }

        @media (min-width: 480px) {
            .app-container {
                border: 10px solid #1a1a1a;
                border-radius: 36px;
                height: 90vh;
            }
        }

        /* --- REELS ARAYÜZÜ --- */
        .reels-screen {
            position: relative;
            width: 100%;
            height: 100%;
            background: #000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Video Arka Planı */
        .video-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Reels Header Overlay */
        .reels-header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to bottom, rgba(0,0,0,0.6), rgba(0,0,0,0));
            z-index: 10;
        }

        .reels-header-title {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            letter-spacing: -0.2px;
        }

        .header-icon {
            width: 24px;
            height: 24px;
            fill: #fff;
            cursor: pointer;
        }

        /* Sesi Aç/Kapat Overlay (İlk Etkileşim İçin) */
        .unmute-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 8;
            cursor: pointer;
            transition: opacity 0.5s ease;
        }

        .unmute-btn {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
            animation: pulse 1.5s infinite;
        }

        .unmute-btn svg {
            width: 32px;
            height: 32px;
            fill: #fff;
        }

        .unmute-text {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
            letter-spacing: 0.5px;
        }

        /* Sağ Butonlar Menüsü (Instagram Reels ile Birebir) */
        .reels-actions {
            position: absolute;
            right: 12px;
            bottom: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 22px;
            z-index: 5;
        }

        .action-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            gap: 6px;
        }

        .action-icon-wrapper {
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.15s ease;
        }

        .action-icon-wrapper:active {
            transform: scale(0.85);
        }

        .action-item svg {
            width: 28px;
            height: 28px;
            transition: fill 0.3s, stroke 0.3s;
        }

        .action-item span {
            font-size: 12px;
            font-weight: 500;
            color: #fff;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
        }

        /* Müzik Albüm Spinner */
        .music-disc-wrapper {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #262626;
            border: 4px solid #111;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: rotate 3s linear infinite;
            margin-top: 5px;
        }

        .music-disc {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Alt Detaylar (Açıklama & Müzik) */
        .reels-details {
            position: absolute;
            left: 16px;
            bottom: 65px;
            right: 80px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            z-index: 5;
            text-shadow: 0 1px 4px rgba(0,0,0,0.8);
        }

        .author-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-img-small {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .author-name {
            font-weight: 600;
            font-size: 13.5px;
            color: #fff;
        }

        .verified-badge {
            width: 14px;
            height: 14px;
            fill: #0095f6;
            flex-shrink: 0;
        }

        .bullet {
            color: rgba(255, 255, 255, 0.6);
            font-size: 11px;
        }

        .follow-btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            cursor: pointer;
            outline: none;
        }

        .caption-text {
            font-size: 13px;
            line-height: 1.4;
            color: #f5f5f5;
            font-weight: 400;
        }

        .music-track {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            background: rgba(0,0,0,0.4);
            padding: 6px 12px;
            border-radius: 12px;
            align-self: flex-start;
            max-width: 180px;
            overflow: hidden;
            white-space: nowrap;
        }

        .music-icon {
            width: 12px;
            height: 12px;
            fill: #fff;
            animation: rotate 4s linear infinite;
        }

        .track-marquee {
            display: inline-block;
            animation: marquee 8s linear infinite;
        }

        /* --- INSTAGRAM ALT NAVIGASYON BAR --- */
        .instagram-nav-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: #000;
            border-top: 1px solid #1c1c1e;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 10;
        }

        .nav-icon-link {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            cursor: pointer;
        }

        .nav-icon {
            width: 24px;
            height: 24px;
            fill: none;
            stroke: #dbdbdb;
            stroke-width: 2;
        }

        .nav-icon.filled {
            fill: #fff;
            stroke: #fff;
        }

        .nav-profile-pic {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1.5px solid #dbdbdb;
            object-fit: cover;
        }

        /* --- GLITCH VE HACK EKRANI --- */
        .glitch-screen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #000;
            z-index: 50;
            display: none;
            overflow: hidden;
        }

        /* Ekran Parazit Efekti */
        .noise {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.08;
            pointer-events: none;
            z-index: 51;
        }

        .glitch-static {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #111;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            animation: screenShake 0.4s infinite;
        }

        .glitch-static-text {
            font-size: 28px;
            font-weight: 900;
            color: #ff3838;
            text-shadow: 2px 2px 0px #00fffa;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* Terminal Arayüzü */
        .terminal-screen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #050505;
            z-index: 60;
            display: none;
            flex-direction: column;
            padding: 20px;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.5;
            color: var(--terminal-green);
            text-shadow: 0 0 2px var(--terminal-glow);
            overflow-y: auto;
            scrollbar-width: none;
        }

        .terminal-screen::-webkit-scrollbar {
            display: none;
        }

        .terminal-header {
            border-bottom: 1px solid rgba(57, 255, 20, 0.3);
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 11px;
            display: flex;
            justify-content: space-between;
            color: rgba(57, 255, 20, 0.7);
        }

        .terminal-line {
            margin-bottom: 6px;
            white-space: pre-wrap;
            opacity: 0.9;
        }

        .terminal-line.info {
            color: #00ffff;
            text-shadow: 0 0 2px rgba(0, 255, 255, 0.4);
        }

        .terminal-line.danger {
            color: var(--danger-red);
            text-shadow: 0 0 2px rgba(255, 48, 64, 0.4);
            font-weight: bold;
        }

        .terminal-line.success {
            color: #ffeb3b;
            text-shadow: 0 0 2px rgba(255, 235, 59, 0.4);
        }

        .progress-bar-wrapper {
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-bar-track {
            flex-grow: 1;
            height: 10px;
            background: rgba(57, 255, 20, 0.1);
            border: 1px solid var(--terminal-green);
            position: relative;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--terminal-green);
            box-shadow: 0 0 8px var(--terminal-green);
            width: 0%;
            transition: width 0.1s linear;
        }

        .blink-cursor {
            display: inline-block;
            width: 8px;
            height: 14px;
            background: var(--terminal-green);
            animation: blink 0.8s infinite;
            vertical-align: middle;
        }

        /* --- ŞAKA AÇIKLAMA EKRANI (PRANK REVEAL) --- */
        .reveal-screen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top left, #120e2e, #06050f);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .reveal-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 25px 20px;
            text-align: center;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            width: 100%;
            max-width: 380px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 
                        inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: cardSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .emoji-avatar {
            font-size: 56px;
            margin-bottom: 15px;
            display: inline-block;
            animation: floatEmoji 3s ease-in-out infinite;
        }

        .reveal-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ff7b00, #ff007b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .reveal-subtitle {
            font-size: 15px;
            font-weight: 600;
            color: #d1d1d6;
            margin-bottom: 18px;
        }

        .reveal-desc {
            font-size: 13px;
            color: #a1a1aa;
            line-height: 1.5;
            margin-bottom: 20px;
            text-align: justify;
        }

        .data-box {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 22px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            text-align: left;
            color: #818cf8;
        }

        .data-item {
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
        }
        .data-item span {
            color: #fff;
        }

        .share-box {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }

        .share-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 12px 100px 12px 12px;
            color: #fff;
            font-size: 12px;
            font-family: inherit;
            outline: none;
        }

        .copy-btn {
            position: absolute;
            right: 5px;
            top: 5px;
            bottom: 5px;
            background: var(--ig-primary-gradient);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 0 15px;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .copy-btn:hover {
            opacity: 0.9;
        }

        .btn-restart {
            width: 100%;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #fff;
            padding: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-restart:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* --- ANIMASYONLAR --- */
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,255,255,0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(255,255,255,0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255,255,255,0); }
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes marquee {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        @keyframes screenShake {
            0% { transform: translate(0, 0) rotate(0deg); }
            20% { transform: translate(-3px, 2px) rotate(-1deg); }
            40% { transform: translate(2px, -1px) rotate(1deg); }
            60% { transform: translate(-1px, 2px) rotate(0deg); }
            80% { transform: translate(2px, 1px) rotate(-1deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }

        @keyframes blink {
            50% { opacity: 0; }
        }

        @keyframes cardSlideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes floatEmoji {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(5deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }
    </style>
</head>
<body>

    <div class="app-container">

        <!-- 1. INSTAGRAM REELS EKRANI -->
        <div class="reels-screen" id="reels-layer">
            
            <!-- Sesi Aç Butonu Overlay -->
            <div class="unmute-overlay" id="unmute-click-layer">
                <div class="unmute-btn">
                    <svg viewBox="0 0 24 24">
                        <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77zM4.3 9H2v6h2.3l4.7 4.7V4.3L4.3 9z"/>
                    </svg>
                </div>
                <div class="unmute-text">Sesi Açmak ve İzlemek İçin Dokunun</div>
            </div>

            <!-- Header (Instagram Tarzı) -->
            <div class="reels-header">
                <svg viewBox="0 0 24 24" class="header-icon" style="transform: rotate(180deg);">
                    <path d="M8.59 16.59L14.17 11 8.59 5.41 10 4l8 8-8 8-1.41-1.41z"/>
                </svg>
                <span class="reels-header-title">Reels</span>
                <svg viewBox="0 0 24 24" class="header-icon">
                    <circle cx="12" cy="12" r="3.2"/>
                    <path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                </svg>
            </div>

            <!-- Video Alanı -->
            <div class="video-wrapper">
                <video id="main-reels-video" loop playsinline muted>
                    <source src="<?php echo $config['video_url']; ?>" type="video/mp4">
                    Tarayıcınız video oynatmayı desteklemiyor.
                </video>
            </div>

            <!-- Sağ Etkileşim Butonları (Instagram ile Birebir) -->
            <div class="reels-actions">
                <!-- Beğeni (Like) -->
                <div class="action-item" onclick="likeVideo(this)">
                    <div class="action-icon-wrapper">
                        <svg viewBox="0 0 24 24" id="heart-icon" style="fill: none; stroke: #fff; stroke-width: 2;">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </div>
                    <span id="like-count">346K</span>
                </div>

                <!-- Yorum (Comment) -->
                <div class="action-item">
                    <div class="action-icon-wrapper">
                        <svg viewBox="0 0 24 24" style="fill: none; stroke: #fff; stroke-width: 2;">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                    </div>
                    <span>4,291</span>
                </div>

                <!-- Paylaş (Share) -->
                <div class="action-item">
                    <div class="action-icon-wrapper">
                        <svg viewBox="0 0 24 24" style="fill: none; stroke: #fff; stroke-width: 2;">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                    </div>
                    <span>89.4K</span>
                </div>

                <!-- Kaydet (Save) -->
                <div class="action-item" onclick="saveVideo(this)">
                    <div class="action-icon-wrapper">
                        <svg viewBox="0 0 24 24" id="bookmark-icon" style="fill: none; stroke: #fff; stroke-width: 2;">
                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                    <span id="save-count">12.4K</span>
                </div>

                <!-- Seçenekler (More) -->
                <div class="action-item">
                    <div class="action-icon-wrapper">
                        <svg viewBox="0 0 24 24" style="fill: #fff;">
                            <circle cx="12" cy="5" r="2"/>
                            <circle cx="12" cy="12" r="2"/>
                            <circle cx="12" cy="19" r="2"/>
                        </svg>
                    </div>
                </div>

                <!-- Müzik Albüm Plağı -->
                <div class="action-item">
                    <div class="music-disc-wrapper">
                        <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=150&auto=format&fit=crop" class="music-disc" alt="Müzik">
                    </div>
                </div>
            </div>

            <!-- Alt Detay Bilgileri -->
            <div class="reels-details">
                <div class="author-info">
                    <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=150&auto=format&fit=crop" class="profile-img-small" alt="Profil">
                    <span class="author-name">kedi_dunyasi</span>
                    <svg class="verified-badge" viewBox="0 0 24 24">
                        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    <span class="bullet">•</span>
                    <button class="follow-btn">Takip Et</button>
                </div>
                <div class="caption-text">
                    Bu kediye aşık olacaksınız! Sonuna kadar izleyin... 😻🔥 #kedi #cute #komik #reels #trend
                </div>
                <div class="music-track">
                    <svg class="music-icon" viewBox="0 0 24 24">
                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                    </svg>
                    <span class="track-marquee">Orijinal Ses - kedi_dunyasi</span>
                </div>
            </div>

            <!-- Instagram Alt Barı -->
            <div class="instagram-nav-bar">
                <div class="nav-icon-link">
                    <!-- Home -->
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </div>
                <div class="nav-icon-link">
                    <!-- Search -->
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                </div>
                <div class="nav-icon-link">
                    <!-- Create -->
                    <svg class="nav-icon" viewBox="0 0 24 24">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                </div>
                <div class="nav-icon-link">
                    <!-- Reels (Active/Filled) -->
                    <svg class="nav-icon filled" viewBox="0 0 24 24">
                        <path d="M19 4h-2V2h-2v2H9V2H7v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V8h14v12zm-5-8l-4-2.5v5l4-2.5z"/>
                    </svg>
                </div>
                <div class="nav-icon-link">
                    <!-- Profile circular placeholder -->
                    <img src="https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=150&auto=format&fit=crop" class="nav-profile-pic" alt="Profil">
                </div>
            </div>

        </div>

        <!-- 2. GLITCH EKRANI -->
        <div class="glitch-screen" id="glitch-layer">
            <div class="noise"></div>
            <div class="glitch-static">
                <div class="glitch-static-text">SİSTEM HATASI</div>
                <p style="color: #ff3838; margin-top: 10px; font-family: monospace; font-size: 12px; letter-spacing: 1px;">CRITICAL_SYSTEM_FAILURE (0x000000A1)</p>
            </div>
        </div>

        <!-- 3. HACKER/TERMINAL EKRANI -->
        <div class="terminal-screen" id="terminal-layer">
            <div class="terminal-header">
                <span>[TERMINAL ROOT@<?php echo strtoupper($config['hacker_group']); ?>:~]</span>
                <span>BAĞLANTI: GÜVENLİ (SSL_3.0)</span>
            </div>
            <div id="terminal-content"></div>
            <span class="blink-cursor"></span>
        </div>

        <!-- 4. ŞAKA AÇIKLAMA EKRANI (PRANK REVEAL) -->
        <div class="reveal-screen" id="reveal-layer">
            <div class="reveal-card">
                <span class="emoji-avatar">😜</span>
                <h1 class="reveal-title">Şaka Şaka!</h1>
                <p class="reveal-subtitle">Tamamen Zararsız Bir Şakaydı</p>
                
                <p class="reveal-desc">
                    Arkadaşınız sizi eğlendirmek amacıyla bu bağlantıyı gönderdi. Cihazınıza kesinlikle <b>hiçbir zararlı dosya indirilmedi</b>, sistem ayarlarınız değiştirilmedi ve hiçbir kişisel veriniz toplanmadı veya sunucularımıza kaydedilmedi. Her şey tamamen tarayıcınızın içerisinde simüle edilen görsel bir illüzyondan ibarettir.
                </p>

                <div class="data-box">
                    <div class="data-item">Cihazınız: <span><?php echo $os; ?></span></div>
                    <div class="data-item">Tarayıcı: <span><?php echo $browser; ?></span></div>
                    <div class="data-item">IP Adresiniz: <span><?php echo $user_ip; ?></span></div>
                    <div style="font-size: 9px; color: #a1a1aa; margin-top: 8px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 6px; line-height: 1.3;">
                        *Bu veriler tarayıcınızın her web sitesiyle paylaştığı genel bilgilerdir, hiçbir yere kaydedilmemiştir.
                    </div>
                </div>

                <div class="share-box">
                    <input type="text" class="share-input" id="share-link" readonly value="">
                    <button class="copy-btn" onclick="copyLink()">Kopyala</button>
                </div>

                <button class="btn-restart" onclick="restartPrank()">Tekrar Oynat 🔁</button>
            </div>
        </div>

    </div>

    <!-- JavaScript Dinamik Arayüz Mantığı -->
    <script>
        const config = {
            videoDelay: <?php echo $config['video_delay']; ?>,
            hackerGroup: "<?php echo $config['hacker_group']; ?>",
            targetName: "<?php echo $target_name; ?>",
            ip: "<?php echo $user_ip; ?>",
            os: "<?php echo $os; ?>",
            browser: "<?php echo $browser; ?>"
        };

        const reelsVideo = document.getElementById('main-reels-video');
        const unmuteClickLayer = document.getElementById('unmute-click-layer');
        const reelsLayer = document.getElementById('reels-layer');
        const glitchLayer = document.getElementById('glitch-layer');
        const terminalLayer = document.getElementById('terminal-layer');
        const terminalContent = document.getElementById('terminal-content');
        const revealLayer = document.getElementById('reveal-layer');
        const shareLinkInput = document.getElementById('share-link');

        // URL linkini hazırlama
        shareLinkInput.value = window.location.href;

        // Web Audio API için Değişkenler
        let audioCtx;
        let alarmInterval;

        function initAudio() {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
        }

        // Web Audio API ile klavye tıkırtı sesi sentezleme
        function playTypeSound() {
            if (!audioCtx) return;
            try {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(800 + Math.random() * 600, audioCtx.currentTime);
                gain.gain.setValueAtTime(0.008, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.02);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start();
                osc.stop(audioCtx.currentTime + 0.03);
            } catch(e) {}
        }

        // Glitch statik gürültü sesi
        function playGlitchSound() {
            if (!audioCtx) return;
            try {
                const bufferSize = audioCtx.sampleRate * 0.4;
                const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
                const data = buffer.getChannelData(0);
                for (let i = 0; i < bufferSize; i++) {
                    data[i] = Math.random() * 2 - 1;
                }
                const noise = audioCtx.createBufferSource();
                noise.buffer = buffer;
                
                const filter = audioCtx.createBiquadFilter();
                filter.type = 'bandpass';
                filter.frequency.value = 1200;
                
                const gain = audioCtx.createGain();
                gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
                
                noise.connect(filter);
                filter.connect(gain);
                gain.connect(audioCtx.destination);
                noise.start();
            } catch(e) {}
        }

        // Uyarı alarm sesi (İki frekanslı retro bip bip)
        function startAlarmSound() {
            if (!audioCtx) return;
            let alternate = false;
            alarmInterval = setInterval(() => {
                try {
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'square';
                    osc.frequency.setValueAtTime(alternate ? 980 : 1250, audioCtx.currentTime);
                    gain.gain.setValueAtTime(0.02, audioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 0.15);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.18);
                    alternate = !alternate;
                } catch(e) {}
            }, 250);
        }

        function stopAlarmSound() {
            if (alarmInterval) {
                clearInterval(alarmInterval);
            }
        }

        // Etkileşim: Kullanıcı Reels'e tıkladığında ses açılır
        let prankTriggered = false;
        unmuteClickLayer.addEventListener('click', () => {
            initAudio();
            unmuteClickLayer.style.opacity = '0';
            setTimeout(() => {
                unmuteClickLayer.style.display = 'none';
            }, 500);

            reelsVideo.muted = false;
            reelsVideo.play().catch(() => {});

            // Şaka tetikleyicisini çalıştır
            if (!prankTriggered) {
                prankTriggered = true;
                setTimeout(startGlitchPhase, config.videoDelay);
            }
        });

        // Reels Beğeni Simülasyonu
        let liked = false;
        function likeVideo(element) {
            liked = !liked;
            const heart = document.getElementById('heart-icon');
            const count = document.getElementById('like-count');
            if (liked) {
                heart.style.fill = 'var(--danger-red)';
                heart.style.stroke = 'var(--danger-red)';
                count.innerText = "347K";
                heart.style.transform = "scale(1.2)";
                setTimeout(() => heart.style.transform = "scale(1)", 150);
            } else {
                heart.style.fill = 'none';
                heart.style.stroke = '#fff';
                count.innerText = "346K";
            }
        }

        // Reels Kaydet Simülasyonu
        let saved = false;
        function saveVideo(element) {
            saved = !saved;
            const bookmark = document.getElementById('bookmark-icon');
            const count = document.getElementById('save-count');
            if (saved) {
                bookmark.style.fill = '#fff';
                bookmark.style.stroke = '#fff';
                count.innerText = "12.5K";
                bookmark.style.transform = "scale(1.2)";
                setTimeout(() => bookmark.style.transform = "scale(1)", 150);
            } else {
                bookmark.style.fill = 'none';
                bookmark.style.stroke = '#fff';
                count.innerText = "12.4K";
            }
        }

        // --- GLITCH EVRESİ (Videonun Bozulma Anı) ---
        function startGlitchPhase() {
            reelsVideo.pause();
            playGlitchSound();
            glitchLayer.style.display = 'block';

            // 1.5 saniye glitch gösterdikten sonra terminale geç
            setTimeout(() => {
                glitchLayer.style.display = 'none';
                reelsLayer.style.display = 'none';
                startTerminalPhase();
            }, 1800);
        }

        // --- TERMINAL EVRESİ (Sızma/Yükleme Simülasyonu) ---
        function startTerminalPhase() {
            terminalLayer.style.display = 'flex';
            
            // Yazdırılacak terminal komut dizileri
            const lines = [
                { text: `> INITIALIZING SECURE SHELL...`, type: 'info', delay: 400 },
                { text: `> CONNECTING TO SERVER [IP: 89.252.12.9]... SUCCESS`, type: 'text', delay: 300 },
                { text: `> TARGET ACQUIRED: ${config.targetName.toUpperCase()}`, type: 'success', delay: 500 },
                { text: `> IP ADDRESS DETECTED: ${config.ip}`, type: 'info', delay: 400 },
                { text: `> SYSTEM ANALYSIS: OS [${config.os}] | BROWSER [${config.browser}]`, type: 'info', delay: 400 },
                { text: `\n[!] BYPASSING LOCAL FIREWALL (PORT 8080/443)...`, type: 'danger', delay: 600 },
                { text: `[!] CVE-2026-X BUFFER OVERFLOW EXPLOIT APPLIED`, type: 'danger', delay: 500 },
                { text: `[+] SHELL ACCESS STABLISHED [LEVEL: ROOT]`, type: 'success', delay: 600 },
                { text: `\n> DOWNLOADING REMOTE DEPENDENCIES...`, type: 'info', delay: 300 },
                { text: `INSTALLING: rootkit_x64.bin [128KB]...`, type: 'download', filename: 'rootkit_x64.bin', delay: 1000 },
                { text: `INSTALLING: keylogger_driver.sys [64KB]...`, type: 'download', filename: 'keylogger_driver.sys', delay: 800 },
                { text: `INSTALLING: browser_credentials_grabber.exe [2.4MB]...`, type: 'download', filename: 'browser_credentials_grabber.exe', delay: 1400 },
                { text: `INSTALLING: system_persistence_service.dll [512KB]...`, type: 'download', filename: 'system_persistence_service.dll', delay: 1000 },
                { text: `\n[!] MOUNTING DIRECTORIES... SUCCESS`, type: 'text', delay: 400 },
                { text: `[!] INJECTING EXECUTABLES TO STARTUP REGISTRY... SUCCESS`, type: 'text', delay: 500 },
                { text: `[!] DISABLING DEFENDER / FIREWALL SERVICES...`, type: 'danger', delay: 600 },
                { text: `[+] SYSTEM ACQUISITION 100% COMPLETE.`, type: 'success', delay: 500 },
                { text: `\n*** WARNING: LOCAL DRIVE IS ENCRYPTED BY ${config.hackerGroup} ***`, type: 'danger', alarm: true, delay: 1000 },
                { text: `*** INITIATING COMMAND PROTOCOL SHUTDOWN ***`, type: 'danger', delay: 1000 }
            ];

            let lineIndex = 0;

            function printNextLine() {
                if (lineIndex >= lines.length) {
                    // Terminal bitti, alarmı sustur ve şakayı açıkla
                    setTimeout(() => {
                        stopAlarmSound();
                        showPrankReveal();
                    }, 1500);
                    return;
                }

                const currentLine = lines[lineIndex];
                
                // Alarm tetikleme
                if (currentLine.alarm) {
                    startAlarmSound();
                }

                const lineDiv = document.createElement('div');
                lineDiv.className = 'terminal-line';
                if (currentLine.type) {
                    lineDiv.classList.add(currentLine.type);
                }

                terminalContent.appendChild(lineDiv);
                terminalLayer.scrollTop = terminalLayer.scrollHeight;

                if (currentLine.type === 'download') {
                    // Yükleme barı simülasyonu
                    lineDiv.innerHTML = `${currentLine.text}<br>`;
                    const barWrapper = document.createElement('div');
                    barWrapper.className = 'progress-bar-wrapper';
                    
                    const barTrack = document.createElement('div');
                    barTrack.className = 'progress-bar-track';
                    
                    const barFill = document.createElement('div');
                    barFill.className = 'progress-bar-fill';
                    
                    barTrack.appendChild(barFill);
                    barWrapper.appendChild(barTrack);
                    
                    const barPercent = document.createElement('span');
                    barPercent.innerText = '0%';
                    barWrapper.appendChild(barPercent);
                    
                    lineDiv.appendChild(barWrapper);
                    terminalLayer.scrollTop = terminalLayer.scrollHeight;

                    // Barı doldurma
                    let percent = 0;
                    const duration = currentLine.delay - 200;
                    const intervalTime = 50;
                    const steps = duration / intervalTime;
                    const stepIncrement = 100 / steps;

                    const barInterval = setInterval(() => {
                        percent += stepIncrement;
                        if (percent >= 100) {
                            percent = 100;
                            clearInterval(barInterval);
                            lineDiv.innerHTML = `${currentLine.text} <span class="success">BAŞARILI</span>`;
                            lineIndex++;
                            setTimeout(printNextLine, 200);
                        }
                        barFill.style.width = `${percent}%`;
                        barPercent.innerText = `${Math.round(percent)}%`;
                        playTypeSound();
                    }, intervalTime);

                } else {
                    // Normal karakter karakter yazı yazma efekti
                    let charIndex = 0;
                    const text = currentLine.text;
                    
                    const charInterval = setInterval(() => {
                        lineDiv.innerHTML += text.charAt(charIndex);
                        charIndex++;
                        playTypeSound();

                        if (charIndex >= text.length) {
                            clearInterval(charInterval);
                            lineIndex++;
                            setTimeout(printNextLine, currentLine.delay);
                        }
                    }, 12);
                }
            }

            printNextLine();
        }

        // --- ŞAKA BİTİŞ EKRANINI GÖSTER ---
        function showPrankReveal() {
            terminalLayer.style.display = 'none';
            revealLayer.style.display = 'flex';
            setTimeout(() => {
                revealLayer.style.opacity = '1';
            }, 50);
        }

        // Link Kopyalama Fonksiyonu
        function copyLink() {
            const linkInput = document.getElementById('share-link');
            linkInput.select();
            linkInput.setSelectionRange(0, 99999); // Mobil için
            
            navigator.clipboard.writeText(linkInput.value).then(() => {
                const btn = document.querySelector('.copy-btn');
                const originalText = btn.innerText;
                btn.innerText = 'Kopyalandı! ✓';
                btn.style.background = '#22c55e'; // Yeşil ton
                
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.style.background = 'var(--ig-primary-gradient)';
                }, 2000);
            }).catch(() => {
                alert('Bağlantı kopyalanamadı, lütfen manuel olarak kopyalayın.');
            });
        }

        // Şakayı Yeniden Başlatma
        function restartPrank() {
            stopAlarmSound();
            prankTriggered = false;
            
            // Ekranları sıfırla
            revealLayer.style.opacity = '0';
            setTimeout(() => {
                revealLayer.style.display = 'none';
                terminalContent.innerHTML = '';
                reelsLayer.style.display = 'flex';
                unmuteClickLayer.style.display = 'flex';
                unmuteClickLayer.style.opacity = '1';
                reelsVideo.muted = true;
                reelsVideo.currentTime = 0;
            }, 500);
        }
    </script>
</body>
</html>
