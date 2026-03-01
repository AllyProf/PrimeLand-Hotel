@extends('dashboard.layouts.app')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
<style>
    :root {
        --brand-orange: #e77a3a;
        --brand-dark: #1a1a1a;
        --accent-red: #940000;
        --gold: #d4af37;
    }

    /* PREVIEW UI STYLES */
    .generator-tile { border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    .preview-window { background: #f8f9fa; border-radius: 12px; padding: 20px; min-height: 500px; border: 2px dashed #ddd; }

    /* POSTER DESIGN (SCREEN & PRINT) */
    .qr-poster-card {
        background: white;
        width: 100%;
        max-width: 480px;
        margin: 0 auto 40px;
        font-family: 'Montserrat', sans-serif;
        display: flex;
        flex-direction: column;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        border: 1px solid #eee;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .poster-top { padding: 40px 20px; text-align: center; flex-grow: 1; position: relative; }
    .poster-accent { position: absolute; top: 0; left: 0; width: 100%; height: 10px; background: linear-gradient(90deg, var(--accent-red), var(--brand-orange)); }
    
    .slogan { font-size: 14px; font-weight: 700; color: var(--accent-red); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
    .hotel-name { font-size: 36px; font-weight: 900; color: var(--brand-dark); text-transform: uppercase; margin-bottom: 30px; line-height: 1; }
    
    .qr-box { background: white; padding: 15px; border-radius: 15px; display: inline-block; position: relative; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .qr-logo-centered { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 45px; height: 45px; background: white; border-radius: 50%; padding: 8px; }

    .poster-bottom {
        background: var(--brand-dark) !important;
        color: white !important;
        padding: 50px 30px;
        text-align: center;
        border-top-left-radius: 40px;
        border-top-right-radius: 40px;
        margin-top: -30px;
        position: relative;
        z-index: 5;
    }

    .cta { font-size: 18px; font-weight: 800; letter-spacing: 2px; margin-bottom: 5px; color: white !important; }
    .room-label { font-size: 56px; font-weight: 900; color: var(--brand-orange) !important; margin-bottom: 12px; }
    .desc { font-size: 13px; color: rgba(255,255,255,0.85) !important; line-height: 1.5; margin-bottom: 25px; }
    .developer { font-size: 10px; color: rgba(255,255,255,0.4) !important; border-top: 1px solid var(--gold); padding-top: 15px; text-transform: uppercase; }

    /* BAR REPORT PRINT LOGIC */
    @media print {
        @page { size: A4 portrait; margin: 5mm; }
        
        body { background: white !important; margin: 0 !important; }
        
        /* Standard Bar Report Hiding */
        .app-header, .app-sidebar, .app-title, .app-breadcrumb, 
        .d-print-none, .generator-sidebar-col, .tile-title {
            display: none !important;
        }

        /* Essential Dashboard Container Reset */
        .app-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            position: static !important;
        }

        #printable_posters_area {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
        }

        .qr-poster-card {
            max-width: 100% !important;
            height: 285mm !important;
            margin: 0 0 10mm 0 !important;
            page-break-after: always !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        /* Scale Up for Paper */
        .hotel-name { font-size: 58pt !important; }
        .room-label { font-size: 88pt !important; }
        .cta { font-size: 28pt !important; }
        .desc { font-size: 16pt !important; }
        .qr-box { padding: 25mm !important; }
        .qr-logo-centered { width: 35mm !important; height: 35mm !important; }
        .poster-bottom { padding: 40mm 20mm !important; }
    }
</style>
@endsection

@section('content')
<div class="app-title d-print-none">
  <div>
    <h1><i class="fa fa-qrcode"></i> Official QR Designer</h1>
    <p>Premium printable posters for guest rooms.</p>
  </div>
</div>

<div class="row">
  <!-- Sidebar Controls: Marked for hiding -->
  <div class="col-md-4 generator-sidebar-col d-print-none">
    <div class="tile generator-tile">
      <div class="form-group">
        <label class="font-weight-bold">1. Select Rooms</label>
        <select class="form-control" id="room_select" multiple style="height: 180px;">
          <option value="general" selected>WELCOME POSTER (Lobby)</option>
          <option value="feedback">GUEST FEEDBACK (Checkout)</option>
          @foreach($rooms as $room)
            <option value="{{ $room->id }}"
              data-number="{{ $room->room_number }}"
              data-url="{{ url('/hotel/services?room=' . $room->room_number) }}">
              Room {{ $room->room_number }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="font-weight-bold">2. Brand Name</label>
        <input type="text" id="hotel_name" class="form-control" value="PRIME LAND HOTEL">
      </div>

      <button class="btn btn-primary btn-block btn-lg" id="btn_generate" style="background: var(--brand-orange); border: none;">
        <i class="fa fa-magic"></i> Generate Posters
      </button>
      
      <button class="btn btn-success btn-block btn-lg mt-2" id="btn_print" style="display:none;">
        <i class="fa fa-print"></i> Print Now (A4)
      </button>
    </div>
  </div>

  <!-- Content Area: This is NEVER hidden as a whole -->
  <div class="col-md-8 d-print-none">
    <div class="tile generator-tile" style="min-height: 600px;">
      <h3 class="tile-title text-center">Preview Output</h3>
      <div class="preview-window" id="screen_preview">
         <div class="text-center py-5" id="place_holder">
            <i class="fa fa-qrcode" style="font-size: 5rem; opacity: 0.1;"></i>
            <h5 class="text-muted mt-3">Ready to generate posters</h5>
         </div>
      </div>
    </div>
  </div>
</div>

<!-- PRINT AREA: Outside any d-print-none containers -->
<div id="printable_posters_area"></div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function() {
    const logoUrl = "{{ asset('royal-master/image/logo/Logo.png') }}";

    $('#btn_generate').on('click', function() {
        const options = $('#room_select option:selected').toArray();
        if (options.length === 0) return;

        $('#place_holder').hide();
        const brand = $('#hotel_name').val();
        const screen = $('#screen_preview');
        const print = $('#printable_posters_area');
        
        screen.empty();
        print.empty();

        options.forEach((opt) => {
            const isGen = opt.value === 'general';
            const isFeedback = opt.value === 'feedback';
            
            // Build absolute URL based on current environment
            const baseOrigin = window.location.origin;
            const feedbackUrl = baseOrigin + "/customer/feedback"; // General feedback link
            const servicesUrl = baseOrigin + "/hotel/services";
            
            const roomData = {
                id: isGen ? 'g' : (isFeedback ? 'f' : opt.value),
                num: isGen ? 'SERVICES' : (isFeedback ? 'FEEDBACK' : 'ROOM ' + opt.dataset.number),
                url: isGen ? servicesUrl : (isFeedback ? feedbackUrl : opt.dataset.url),
                cta: isFeedback ? "YOUR FEEDBACK" : "SCAN & EXPLORE",
                slogan: isFeedback ? "We Value Your Experience" : "Comfort in Every Stay",
                desc: isFeedback ? 
                    "How was your stay? We strive for excellence and would love to hear your thoughts. Scan to share your experience with us." : 
                    "Explore our digital menu, request room services, and manage your stay directly from your smartphone. Experience seamless luxury."
            };
            
            const qrid = `q_${roomData.id}`;
            const pqrid = `pq_${roomData.id}`;
            
            const layout = (id) => `
                <div class="qr-poster-card">
                    <div class="poster-top">
                        <div class="poster-accent"></div>
                        <div class="slogan">${roomData.slogan}</div>
                        <div class="hotel-name">${brand}</div>
                        <div class="qr-box">
                            <div id="${id}"></div>
                            <div class="qr-logo-centered">
                                <img src="${logoUrl}" alt="Logo" style="width:100%; height:auto;">
                            </div>
                        </div>
                    </div>
                    <div class="poster-bottom">
                        <div class="cta">${roomData.cta}</div>
                        <div class="room-label">${roomData.num}</div>
                        <p class="desc">${roomData.desc}</p>
                        <div class="developer">Powered By <a href="https://www.emca.tech" target="_blank" style="color: #940000; font-weight: bold; text-decoration: none;">EmCa Techonologies</a></div>
                    </div>
                </div>`;
            
            screen.append(layout(qrid));
            print.append(layout(pqrid));

            setTimeout(() => {
                new QRCode(document.getElementById(qrid), {
                    text: roomData.url, width: 260, height: 260,
                    correctLevel: QRCode.CorrectLevel.H, colorDark: "#1a1a1a"
                });

                new QRCode(document.getElementById(pqrid), {
                    text: roomData.url, width: 600, height: 600,
                    correctLevel: QRCode.CorrectLevel.H, colorDark: "#1a1a1a"
                });
            }, 60);
        });

        $('#btn_print').show();
        $('html, body').animate({ scrollTop: $('#screen_preview').offset().top - 20 }, 500);
    });

    $('#btn_print').on('click', function() {
        window.print();
    });
});
</script>
@endsection
