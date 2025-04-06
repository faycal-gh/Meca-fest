<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Success - Meca-Fest 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <style>
        body {
            font-family: "Press Start 2P", system-ui;
            background-color: #1a202c;
            color: white;
            margin: 0;
            overflow: hidden;
        }
        
        #canvas-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        .success-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(31, 41, 55, 0.7);
            border: 1px solid white;
            padding: 2rem;
            border-radius: 0.75rem;
            text-align: center;
            max-width: 80%;
            width: 600px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            z-index: 10;            
        }
        
        h1 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            letter-spacing: 2px;
            color: #4ade80;
            text-shadow: 2px 2px 0px #1a202c;
        }
        
        p {
            font-size: 0.7rem;
            line-height: 1.5rem;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
        }
        
        .buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .button {
            padding: 0.75rem 1.5rem;
            background-color: transparent;
            border: 1px solid white;
            color: white;
            border-radius: 0.5rem;
            font-family: "Press Start 2P", system-ui;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .button:hover {
            background-color: white;
            color: #1a202c;
        }
        
        .success-icon {
            margin-bottom: 1.5rem;
        }
                
        @media (max-width: 768px) {
            .success-container {
                padding: 1.5rem;
                max-width: 85%;
            }
            
            h1 {
                font-size: 1rem;
            }
            
            p {
                font-size: 0.6rem;
                line-height: 1.2rem;
            }
            
            .button {
                padding: 0.5rem 1rem;
                font-size: 0.6rem;
            }
            
            .success-icon svg {
                width: 48px;
                height: 48px;
            }
        }
        
        @media (max-width: 480px) {
            .success-container {
                padding: 1rem;
                max-width: 80%;
            }
            
            h1 {
                font-size: 0.8rem;
                margin-bottom: 1rem;
                line-height: 2;
            }
            
            p {
                font-size: 0.5rem;
                line-height: 1rem;
                margin-bottom: 1rem;
            }
            
            .button {
                padding: 0.4rem 0.8rem;
                font-size: 0.5rem;
            }
            
            .success-icon svg {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <div id="canvas-container"></div>
    
    <div class="success-container">
        <div class="success-icon">
            <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="12" y="12" width="8" height="8" fill="#4ade80" />
                <rect x="20" y="20" width="8" height="8" fill="#4ade80" />
                <rect x="28" y="28" width="8" height="8" fill="#4ade80" />
                <rect x="36" y="20" width="8" height="8" fill="#4ade80" />
                <rect x="44" y="12" width="8" height="8" fill="#4ade80" />
                <rect x="12" y="44" width="40" height="8" fill="#4ade80" />
            </svg>
        </div>
        <h1>REGISTRATION COMPLETE!</h1>
        <p id="message1"></p>
        <p id="message2"></p>
        <div class="buttons">
            <a href="index.php" class="button">BACK TO HOME</a>
        </div>
    </div>

    <script src="rocket.js"></script>
    <script>
        function typeEffect(elementId, text, delay) {
            const element = document.getElementById(elementId);
            let index = 0;

            function type() {
                if (index < text.length) {
                    element.innerHTML += text.charAt(index);
                    index++;
                    setTimeout(type, delay);
                }
            }

            type();
        }        
        const message1 = "Your registration for Meca-Fest 2.0 has been successfully completed. We are excited to see your rocket project!";
        const message2 = "Further instructions and updates will be sent to your email. Make sure to check your inbox regularly.";
        
        typeEffect("message1", message1, 50);
        setTimeout(() => {
            typeEffect("message2", message2, 50);
        }, message1.length * 50 + 100); 
    </script>
</body>
</html>