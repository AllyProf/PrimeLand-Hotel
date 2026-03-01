<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mobile Check-In - PrimeLand Hotel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #e77a3a;
            --secondary: #2c3e50;
            --light: #f8f9fa;
            --dark: #343a40;
            --success: #28a745;
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            background: #f0f2f5; 
            margin: 0; 
            color: var(--dark);
            overscroll-behavior: none;
        }

        .header {
            background: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header h1 { font-size: 18px; margin: 0; color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        .container { max-width: 500px; margin: 0 auto; padding: 20px; }

        .card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 1;
        }

        .step {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            z-index: 2;
            transition: all 0.3s;
            color: #999;
        }

        .step.active {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
            box-shadow: 0 0 10px rgba(231, 122, 58, 0.3);
        }

        .step.done {
            border-color: var(--success);
            background: var(--success);
            color: white;
        }

        h2 { font-size: 20px; margin-bottom: 8px; font-weight: 700; }
        p.subtitle { color: #666; font-size: 14px; margin-bottom: 24px; }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: var(--secondary); }
        select, input {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: 1.5px solid #e0e0e0;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .scan-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .scan-target {
            background: #f8f9fa;
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            overflow: hidden;
            position: relative;
            transition: all 0.3s;
        }

        .scan-target.active { border-color: var(--primary); background: #fff5ef; }
        .scan-target.captured { border-style: solid; border-color: var(--success); }

        .scan-target i { font-size: 24px; color: #999; margin-bottom: 8px; }
        .scan-target span { font-size: 12px; font-weight: 600; color: #666; }
        
        .scan-target img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #000;
            z-index: 2000;
            display: none;
            flex-direction: column;
        }

        #video { width: 100%; height: 80%; object-fit: cover; }
        
        .camera-controls {
            flex: 1;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 20px;
        }

        .shutter {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: white;
            border: 5px solid rgba(255,255,255,0.3);
        }

        .btn {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { background: transparent; border: 1.5px solid var(--primary); color: var(--primary); margin-top: 10px; }

        #sig-canvas {
            width: 100%;
            height: 250px;
            background: #fafafa;
            border: 1px dashed var(--primary);
            border-radius: 12px;
            touch-action: none;
        }

        .hidden { display: none; }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 3000;
            display: none;
        }

        .loader {
            width: 48px;
            height: 48px;
            border: 5px solid #FFF;
            border-bottom-color: var(--primary);
            border-radius: 50%;
            display: inline-block;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        .success-screen { text-align: center; padding: 40px 20px; }
        .success-icon { font-size: 80px; color: var(--success); margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>PrimeLand Hotel</h1>
        <div style="font-size: 12px; color: #666;">Official Guest Check-In</div>
    </div>

    <div class="container" id="main-content">
        <div class="step-indicator">
            <div class="step active" id="dot-1">1</div>
            <div class="step" id="dot-2">2</div>
            <div class="step" id="dot-3">3</div>
        </div>

        <!-- Step 1: ID Capture (Multi-Side) -->
        <div id="step-1" class="card">
            <h2>Identity Documents</h2>
            <p class="subtitle">Please scan your ID (Optional if not on hand).</p>
            
            <div class="form-group">
                <label>Document Type</label>
                <select id="id_type">
                    <option value="Passport">Passport</option>
                    <option value="National ID">National ID</option>
                    <option value="Driver's License">Driver's License</option>
                </select>
            </div>

            <div class="form-group">
                <label>ID Document Number (Optional)</label>
                <input type="text" id="id_number" placeholder="Enter ID number (if available)">
            </div>

            <label>Tap to capture photos</label>
            <div class="scan-wrapper">
                <div class="scan-target" id="scan-front" onclick="openCamera('front')">
                    <i class="fa fa-id-card"></i>
                    <span>FRONT SIDE</span>
                    <span style="font-size: 10px; color: #999; margin-top: 5px;">(Optional)</span>
                    <img id="img-front" class="hidden">
                </div>
                <div class="scan-target" id="scan-back" onclick="openCamera('back')">
                    <i class="fa fa-id-card"></i>
                    <span>BACK SIDE</span>
                    <img id="img-back" class="hidden">
                    <span style="font-size: 10px; color: #999; margin-top: 5px;">(Optional for Passport)</span>
                </div>
            </div>

            <div style="margin-top: 15px; font-size: 13px; color: #666; background: #fff8f4; padding: 10px; border-radius: 8px; border: 1px solid #ffd8c1;">
                <i class="fa fa-info-circle" style="color: var(--primary);"></i> Ensure the text is clear and readable.
            </div>

            <button class="btn btn-primary" style="margin-top: 25px;" id="next-to-2">
                Continue to Signature <i class="fa fa-arrow-right"></i>
            </button>
            <input type="file" id="manual-file" accept="image/*" class="hidden">
            <button class="btn btn-outline" onclick="document.getElementById('manual-file').click()">
                <i class="fa fa-upload"></i> Upload manually instead
            </button>
        </div>

        <!-- Step 2: Signature -->
        <div id="step-2" class="card hidden">
            <h2>Guest Signature</h2>
            <p class="subtitle">Please sign inside the box below.</p>
            
            <div style="position: relative;">
                <canvas id="sig-canvas"></canvas>
                <button id="clear-sig" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: #999;">
                    <i class="fa fa-undo"></i> Clear
                </button>
            </div>

            <div style="margin-top: 25px;">
                <button class="btn btn-primary" id="submit-btn">
                    Complete Check-In <i class="fa fa-check-circle"></i>
                </button>
                <button class="btn btn-outline" id="back-to-1">
                    <i class="fa fa-arrow-left"></i> Back
                </button>
            </div>
        </div>

        <!-- Step 3: Success (Submitted for Review) -->
        <div id="step-3" class="card hidden success-screen">
            <div class="success-icon"><i class="fa fa-clock" style="color: #03a9f4;"></i></div>
            <h2>Records Submitted!</h2>
            <p>Thank you, **{{ $booking->guest_name }}**.</p>
            <p style="font-size: 14px; color: #666; margin-top: 10px;">Your identity details and signature have been sent to our reception for review.</p>
            
            <div class="alert alert-info mt-3" style="font-size: 14px; background: #e3f2fd; border: none; border-radius: 12px; color: #01579b;">
                <i class="fa fa-info-circle"></i> Please inform the receptionist that you have finished the scan. They will verify your records and hand you your room key.
            </div>

            <div style="background: var(--light); padding: 15px; border-radius: 12px; margin-top: 25px; text-align: left;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span style="font-size: 12px; color: #999;">REF:</span>
                    <span style="font-weight: 700;">{{ $booking->booking_reference }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Camera UI -->
    <div class="camera-overlay" id="camera-ui">
        <video id="video" autoplay playsinline></video>
        <div class="camera-controls">
            <button style="background: none; border: none; color: white;" onclick="closeCamera()">CANCEL</button>
            <div class="shutter" id="shutter"></div>
            <div style="width: 50px;"></div>
        </div>
        <canvas id="hidden-canvas" class="hidden"></canvas>
    </div>

    <div class="loading-overlay" id="loading">
        <span class="loader"></span>
        <p style="margin-top: 15px; font-weight: 600;">Processing submission...</p>
    </div>

    <script>
        let currentScanSide = null;
        let scans = { front: null, back: null };
        let signaturePad = null;
        let stream = null;

        async function openCamera(side) {
            currentScanSide = side;
            document.getElementById('camera-ui').style.display = 'flex';
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'environment', width: { ideal: 1280 } } 
                });
                document.getElementById('video').srcObject = stream;
            } catch (err) {
                console.error(err);
                closeCamera();
                Swal.fire('Camera Error', 'Access denied or unavailable.', 'error');
            }
        }

        function closeCamera() {
            document.getElementById('camera-ui').style.display = 'none';
            if (stream) stream.getTracks().forEach(t => t.stop());
        }

        document.getElementById('shutter').onclick = () => {
            const video = document.getElementById('video');
            const canvas = document.getElementById('hidden-canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            savePhoto(dataUrl);
            closeCamera();
        };

        function savePhoto(dataUrl) {
            scans[currentScanSide] = dataUrl;
            const img = document.getElementById(`img-${currentScanSide}`);
            img.src = dataUrl;
            img.classList.remove('hidden');
            document.getElementById(`scan-${currentScanSide}`).classList.add('captured');
        }

        // Manual Upload Handle
        document.getElementById('manual-file').onchange = (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (re) => {
                // If front is missing, use it, else back
                if (!scans.front) {
                    currentScanSide = 'front';
                } else {
                    currentScanSide = 'back';
                }
                savePhoto(re.target.result);
            };
            reader.readAsDataURL(file);
        };

        // Signature Init
        function initSignature() {
            if (signaturePad) return;
            const canvas = document.getElementById('sig-canvas');
            const ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });

            // Fix Clear Button
            document.getElementById('clear-sig').onclick = () => {
                signaturePad.clear();
            };
        }

        document.getElementById('next-to-2').onclick = () => {
            // ID is now optional, just proceed
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');
            document.getElementById('dot-1').classList.add('done');
            document.getElementById('dot-1').innerHTML = '✓';
            document.getElementById('dot-2').classList.add('active');
            initSignature();
        };

        document.getElementById('back-to-1').onclick = () => {
            document.getElementById('step-1').classList.remove('hidden');
            document.getElementById('step-2').classList.add('hidden');
        };

        document.getElementById('submit-btn').onclick = async () => {
            if (signaturePad.isEmpty()) {
                Swal.fire('Signed Required', 'Please sign the document.', 'warning');
                return;
            }

            document.getElementById('loading').style.display = 'flex';

            const payload = {
                id_document_type: document.getElementById('id_type').value,
                id_document_number: document.getElementById('id_number').value,
                id_scan_front: scans.front,
                id_scan_back: scans.back,
                guest_signature: signaturePad.toDataURL()
            };

            try {
                const res = await fetch('{{ route("checkin.mobile.submit", $booking->checkin_token) }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify(payload)
                });
                
                if (!res.ok) {
                    const errorText = await res.text();
                    console.error('Submission failed:', res.status, errorText);
                    try {
                        const errorJson = JSON.parse(errorText);
                        throw new Error(errorJson.message || `Server error: ${res.status}`);
                    } catch(e) {
                        throw new Error(`Server error: ${res.status}`);
                    }
                }

                const data = await res.json();
                document.getElementById('loading').style.display = 'none';

                if (data.success) {
                    document.getElementById('step-2').classList.add('hidden');
                    document.getElementById('step-3').classList.remove('hidden');
                    
                    // Simple interval to see if reception finally checks them in
                    const pollInterval = setInterval(async () => {
                        try {
                            const pollRes = await fetch(`/check-in/status/{{ $booking->id }}`);
                            const pollData = await pollRes.json();
                            if (pollData.is_checked_in) {
                                clearInterval(pollInterval);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Welcome!',
                                    text: 'Reception has verified your records and checked you in.',
                                    confirmButtonText: 'Great!'
                                });
                            }
                        } catch(e) {}
                    }, 5000);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (err) {
                document.getElementById('loading').style.display = 'none';
                console.error("Submission Error:", err);
                Swal.fire('Submission Failed', err.message || 'Connection failed or server error.', 'error');
            }
        };
    </script>
</body>
</html>
