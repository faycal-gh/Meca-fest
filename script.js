const text = "Gear up for meXperience!";
let index = 0;
function typeWriter() {
    if (index < text.length) {
        document.getElementById("typing-text").textContent += text.charAt(index);
        index++;
        setTimeout(typeWriter, 80); 
    }
}
window.onload = function () {
    typeWriter(); 
    setupScene(); 
};
let scene, camera, renderer, lines;
function setupScene() {
    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });

    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    document.body.appendChild(renderer.domElement);

    const noise = (x, y, z) => {
        const p = new THREE.Vector3(x, y, z);
        const n = p.dot(new THREE.Vector3(12.9898, 78.233, 37.719));
        return Math.abs(Math.sin(n) * 43758.5453 % 1);
    };

    const curve = (t, i, time) => {

        return Math.sin(t * 2 + time * 0.7 + i * 0.2) * 1.2 +
            Math.sin(t * 5 + time * 0.5 + i * 0.5) * 0.5 +
            Math.sin(t * 8 + time * 0.3 + i) * 0.3;
    };

    lines = [];
    const totalLines = 60;  
    const pointsPerLine = 150;  
    const amplitude = 20;  
    const spread = 100;  

    for (let i = 0; i < totalLines; i++) {
        const linePoints = [];
        const offsetX = (Math.random() - 0.5) * 5;
        const offsetY = (Math.random() - 0.5) * 5;
        const offsetZ = (Math.random() - 0.5) * 5;
        const freqMultiplier = 0.01 + Math.random() * 0.03;
        for (let j = 0; j < pointsPerLine; j++) {
            const t = j / pointsPerLine;
            const x = (t * spread * 2 - spread) + offsetX;
            const y = Math.sin(j * freqMultiplier * 3 + i * 0.4) * amplitude * 0.7 +
                Math.sin(j * freqMultiplier * 7 + i * 0.2) * amplitude * 0.3 +
                (i - totalLines / 2) * 0.4;
            const z = -20 + i * 0.6 + Math.sin(j * freqMultiplier * 5) * 2;

            linePoints.push(new THREE.Vector3(x, y, z));
        }

        const curve = new THREE.CatmullRomCurve3(linePoints);
        const smoothPoints = curve.getPoints(pointsPerLine);

        const geometry = new THREE.BufferGeometry().setFromPoints(smoothPoints);

        const opacity = 0.15 + Math.random() * 0.25;
        const material = new THREE.LineBasicMaterial({
            color: 0xffffff,
            transparent: true,
            opacity: opacity
        });

        const line = new THREE.Line(geometry, material);
        scene.add(line);

        lines.push({
            line,
            initialPoints: smoothPoints.map(p => ({ x: p.x, y: p.y, z: p.z })),
            offset: Math.random() * Math.PI * 2,  
            speed: 0.2 + Math.random() * 0.6,     
            amplitude: 0.5 + Math.random() * 1.5  
        });
    }

    const purpleLight = new THREE.PointLight(0x6a0dad, 1.5, 100);
    purpleLight.position.set(-20, -10, 10);
    scene.add(purpleLight);

    const tealLight = new THREE.PointLight(0x008080, 1.5, 100);
    tealLight.position.set(20, -10, 10);
    scene.add(tealLight);

    const ambientLight = new THREE.AmbientLight(0x333333);
    scene.add(ambientLight);

    camera.position.z = 5;

    animate();
}

function animate() {
    requestAnimationFrame(animate);

    const time = Date.now() * 0.001;
    const pointsPerLine = 150;

    lines.forEach((lineObj, lineIndex) => {
        const positions = lineObj.line.geometry.attributes.position.array;

        for (let j = 0; j < pointsPerLine; j++) {
            const initialPoint = lineObj.initialPoints[j];
            const t = j / pointsPerLine;

            const waveX = Math.sin(time * lineObj.speed + lineObj.offset + t * 5) * lineObj.amplitude;
            const waveY = Math.cos(time * lineObj.speed * 0.7 + lineObj.offset + t * 3) * lineObj.amplitude * 1.2;
            const waveZ = Math.sin(time * lineObj.speed * 0.5 + lineObj.offset + t * 7) * lineObj.amplitude * 0.5;

            const detail = 0.3 * Math.sin(time * 2 + t * 20 + lineIndex * 0.5);

            positions[j * 3] = initialPoint.x + waveX + detail;
            positions[j * 3 + 1] = initialPoint.y + waveY + detail;
            positions[j * 3 + 2] = initialPoint.z + waveZ;
        }

        lineObj.line.geometry.attributes.position.needsUpdate = true;
    });

    scene.rotation.y = Math.sin(time * 0.1) * 0.1;
    scene.rotation.x = Math.cos(time * 0.1) * 0.05;

    camera.position.y = Math.sin(time * 0.05) * 2;

    renderer.render(scene, camera);
}

window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});

document.getElementById('start-game-btn').addEventListener('click', function () {

    gsap.to('.titles', {
        opacity: 0,
        duration: 0.8,
        y: -50,
        ease: "power2.out"
    });

    gsap.to('.features', {
        opacity: 0,
        duration: 0.8,
        scale: 0.8,
        ease: "power2.out"
    });

    gsap.to(scene.position, {
        z: -10,
        duration: 1.5,
        ease: "power2.inOut"
    });

    gsap.to(camera.position, {
        z: 15,
        duration: 1.5,
        ease: "power2.inOut"
    });

    setTimeout(() => {

        document.getElementById('next-section').style.visibility = 'visible';

        gsap.fromTo('#next-section',
            { opacity: 0, scale: 0.9 },
            { opacity: 1, scale: 1, duration: 1, ease: "back.out(1.2)" }
        );

        gsap.to(scene.scale, {
            x: 1.05,
            y: 1.05,
            z: 1.05,
            duration: 2,
            ease: "elastic.out(1, 0.3)"
        });
    }, 800);
});

document.querySelector('button').addEventListener('click', function (e) {
    e.preventDefault(); 
});
document.getElementById('previous-btn').addEventListener('click', function (e) {
    e.preventDefault(); 

});

document.addEventListener('DOMContentLoaded', function () {

    const nextButton = document.getElementById('to-upload-section');
    if (nextButton) {
        nextButton.addEventListener('click', function (e) {
            e.preventDefault(); 

            if (validateRegistrationForm()) {

                gsap.to('#registration-form', {
                    opacity: 0,
                    scale: 0.9,
                    duration: 0.8,
                    ease: "power2.out",
                    onComplete: function () {
                        document.getElementById('registration-form').style.visibility = 'hidden';

                        document.getElementById('upload-section').style.visibility = 'visible';

                        gsap.fromTo('#upload-section',
                            { opacity: 0, scale: 0.9, rotation: -5 },
                            { opacity: 1, scale: 1, rotation: 0, duration: 1, ease: "back.out(1.7)" }
                        );

                        gsap.to(camera.position, {
                            z: 10,
                            y: 3,
                            duration: 1.5,
                            ease: "power2.inOut"
                        });

                        gsap.to(scene.rotation, {
                            y: Math.PI * 0.1,
                            duration: 1.5,
                            ease: "power2.inOut"
                        });
                    }
                });
            }
        });
    }

    function validateRegistrationForm() {

        const email = document.getElementById('email').value.trim();
        const teamCode = document.getElementById('team_code').value.trim();
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const discordId = document.getElementById('skills').value.trim();
        const phoneNumber = document.getElementById('phone_number').value.trim();
        const dateOfBirth = document.getElementById('date_of_birth').value.trim();
        const studentId = document.getElementById('student_id').value.trim();
        const university = document.getElementById('university').value.trim();
        const fieldOfStudy = document.getElementById('field_of_study').value.trim();

        clearErrorMessages();

        let isValid = true;

        if (!email || !validateEmail(email)) {
            showError('email', 'Please enter a valid email address.');
            isValid = false;
        }

        if (!teamCode) {
            showError('team_code', 'Team code is required.');
            isValid = false;
        } else if (teamCode.length > 20) {
            showError('team_code', 'Team code must be 20 characters or less.');
            isValid = false;
        }

        if (!firstName) {
            showError('first_name', 'First name is required.');
            isValid = false;
        }

        if (!lastName) {
            showError('last_name', 'Last name is required.');
            isValid = false;
        }

        if (!discordId) {
            showError('skills', 'Skills are required.');
            isValid = false;
        } else if (discordId.length > 100) {
            showError('skills', 'Skills must be 100 characters or less.');
            isValid = false;
        }

        if (!phoneNumber) {
            showError('phone_number', 'Phone number is required.');
            isValid = false;
        } else if (!validatePhoneNumber(phoneNumber)) {
            showError('phone_number', 'Phone number must be valid and exactly 10 digits.');
            isValid = false;
        } 

        if (!dateOfBirth) {
            showError('date_of_birth', 'Date of birth is required.');
            isValid = false;
        } else {
            const dobDate = new Date(dateOfBirth);
            const today = new Date();

            if (isNaN(dobDate.getTime())) {
                showError('date_of_birth', 'Please enter a valid date.');
                isValid = false;
            } else if (dobDate > today) {
                showError('date_of_birth', 'Date of birth cannot be in the future.');
                isValid = false;
            } else {

                const age = today.getFullYear() - dobDate.getFullYear();

                const hasBirthdayOccurred =
                    today.getMonth() > dobDate.getMonth() ||
                    (today.getMonth() === dobDate.getMonth() && today.getDate() >= dobDate.getDate());
                const actualAge = hasBirthdayOccurred ? age : age - 1;

                if (actualAge < 17) {
                    showError('date_of_birth', 'You must be at least 17 years old.');
                    isValid = false;
                }
            }
        }

        if (!studentId) {
            showError('student_id', 'Student ID is required.');
            isValid = false;
        } else if (!validateStudentID(studentId)) {
            showError('student_id', 'Student ID must be valid and exactly 12 digits.');
            isValid = false;
        }

        if (!university) {
            showError('university', 'University is required.');
            isValid = false;
        } else if (university.length > 100) {
            showError('university', 'University name must be 100 characters or less.');
            isValid = false;
        }

        if (!fieldOfStudy) {
            showError('field_of_study', 'Field of study is required.');
            isValid = false;
        } else if (fieldOfStudy.length > 100) {
            showError('field_of_study', 'Field of study must be 100 characters or less.');
            isValid = false;
        }

        return isValid;
    }

    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function validatePhoneNumber(phone) {        

        const phoneRegex = /^\d{10}$/
        return phoneRegex.test(phone);
    }

    function validateStudentID(studentID) {                
        const studentIdRegex =  /^\d{12}$/
        return studentIdRegex.test(studentID);
    }

    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message text-red-500 text-xs mt-1';
        errorElement.textContent = message;

        field.parentNode.insertBefore(errorElement, field.nextSibling);

        field.classList.add('border-red-500');
    }

    function clearErrorMessages() {

        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(element => element.remove());

        const formFields = document.querySelectorAll('input');
        formFields.forEach(field => field.classList.remove('border-red-500'));
    }
});

document.getElementById('back-btn').addEventListener('click', function (e) {

    e.preventDefault(); 

    gsap.to('#registration-form', {
        opacity: 0,
        scale: 0.9,
        duration: 0.8,
        ease: "power2.out",
        onComplete: function () {
            document.getElementById('registration-form').style.visibility = 'hidden';

            document.getElementById('next-section').style.visibility = 'visible';

            gsap.fromTo('#next-section',
                { opacity: 0, scale: 0.9 },
                { opacity: 1, scale: 1, duration: 1, ease: "back.out(1.2)" }
            );

            gsap.to(camera.position, {
                z: 15,
                y: Math.sin(Date.now() * 0.00005) * 2,
                duration: 1.5,
                ease: "power2.inOut"
            });

            gsap.to(scene.rotation, {
                y: Math.sin(Date.now() * 0.0001) * 0.1,
                duration: 1.5,
                ease: "power2.inOut"
            });
        }
    });
});

const fileUpload = document.getElementById('file-upload');
const uploadSection = document.getElementById('upload-section').querySelector('.custom-bg');
const uploadText = document.querySelector('.upload-text');

fileUpload.addEventListener('change', function (event) {
    const file = event.target.files[0];

    uploadSection.style.border = '2px dashed';

    if (!file) return;

    let messageElement = document.getElementById('upload-message');
    if (!messageElement) {
        messageElement = document.createElement('p');
        messageElement.id = 'upload-message';
        messageElement.className = 'mt-2 text-sm custom-font';
        uploadText.appendChild(messageElement);
    }

    if (file.type !== 'application/pdf') {
        uploadSection.style.border = '2px dashed #f44336'; 
        messageElement.textContent = 'Please upload a PDF file only!';
        messageElement.className = 'mt-2 text-sm text-red-500 custom-font';
        fileUpload.value = ''; 
        return;
    }

    const maxSize = 5 * 1024 * 1024; 
    if (file.size > maxSize) {
        uploadSection.style.border = '2px dashed #f44336'; 
        messageElement.textContent = 'File must be less than 5MB!';
        messageElement.className = 'mt-2 text-sm text-red-500 custom-font';
        fileUpload.value = ''; 
        return;
    }

    uploadSection.style.border = '2px dashed #4CAF50'; 
    messageElement.textContent = `Selected file: ${file.name}`;
    messageElement.className = 'mt-2 text-sm text-green-400 custom-font';

    const uploadImage = document.querySelector('.upload-image');
    if (uploadImage) {

    }
});

document.getElementById('previous-btn').addEventListener('click', function () {
    fileUpload.value = ''; 
    uploadSection.style.border = '2px dashed'; 

    const messageElement = document.getElementById('upload-message');
    if (messageElement) {
        messageElement.remove();
    }
});

document.querySelector('#next-btn').addEventListener('click', function (e) {
    e.preventDefault(); 

    gsap.to('#next-section', {
        opacity: 0,
        scale: 0.9,
        duration: 0.8,
        ease: "power2.out",
        onComplete: function () {
            document.getElementById('next-section').style.visibility = 'hidden';

            document.getElementById('registration-form').style.visibility = 'visible';

            gsap.fromTo('#registration-form',
                { opacity: 0, scale: 0.8, rotation: -10 }, 
                { opacity: 1, scale: 1, rotation: 0, duration: 1.2, ease: "elastic.out(1.2, 0.3)" } 
            );

            gsap.to(camera.position, {
                z: 15, 
                y: 5,  
                duration: 2,
                ease: "power3.inOut"
            });

            gsap.to(scene.rotation, {
                y: Math.PI * 0.2, 
                duration: 2,
                ease: "power3.inOut"
            });
        }
    });
});

document.querySelector('#previous-btn').addEventListener('click', function (e) {
    e.preventDefault(); 

    gsap.to('#upload-section', {
        opacity: 0,
        scale: 0.9,
        duration: 0.8,
        ease: "power2.out",
        onComplete: function () {
            document.getElementById('upload-section').style.visibility = 'hidden';

            document.getElementById('registration-form').style.visibility = 'visible';

            gsap.fromTo('#registration-form',
                { opacity: 0, scale: 0.9 },
                { opacity: 1, scale: 1, duration: 1, ease: "back.out(1.2)" }
            );

            gsap.to(camera.position, {
                z: 15,
                y: Math.sin(Date.now() * 0.00005) * 2,
                duration: 1.5,
                ease: "power2.inOut"
            });

            gsap.to(scene.rotation, {
                y: Math.sin(Date.now() * 0.0001) * 0.1,
                duration: 1.5,
                ease: "power2.inOut"
            });
        }
    });
});