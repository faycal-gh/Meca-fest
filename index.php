<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meca-Fest 2.0</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>

    <link href="https://fonts.cdnfonts.com/css/arcade-classic" rel="stylesheet">

    <link rel="stylesheet" href="style_test.css">
</head>

<body>
    <div class="parent-container">
        <div class="next-section" id="success-section">
            <div class="w-full h-full bg-gray-800 custom-bg p-8 rounded-xl shadow-lg flex flex-col justify-center items-center"
                style="border: 1px solid white; max-width: 600px;">
                <div class="success-icon mb-6">
                    <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="12" y="12" width="8" height="8" fill="#4ade80" />
                        <rect x="20" y="20" width="8" height="8" fill="#4ade80" />
                        <rect x="28" y="28" width="8" height="8" fill="#4ade80" />
                        <rect x="36" y="20" width="8" height="8" fill="#4ade80" />
                        <rect x="44" y="12" width="8" height="8" fill="#4ade80" />
                        <rect x="12" y="44" width="40" height="8" fill="#4ade80" />
                    </svg>
                </div>

                <h1 class="text-center text-xl font-bold mb-6 custom-font text-green-400">REGISTRATION COMPLETE!
                </h1>

                <div class="message-container mb-6 w-full">
                    <div class="message-box flex flex-col items-center">
                        <div class="message-text text-center mb-4">
                            <p class="primary-text custom-font mb-4 text-sm">Your team has been successfully
                                registered for Meca-Fest 2.0. We are excited to see your rocket project!</p>
                            <p class="secondary-text custom-font text-xs">Further instructions and updates will
                                be sent to your email. Make sure to check your inbox regularly.</p>
                        </div>
                    </div>
                </div>

                <div class="button-container" style="display: flex; width: 70%; justify-content: center">
                    <a href="index.php" id="home-btn"
                        class="px-6 py-2 bg-transparent border border-white text-white rounded-lg hover:bg-white hover:text-gray-800 transition custom-font">
                        BACK TO HOME
                    </a>
                </div>
            </div>
        </div>
        <?php

        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1); 
        ini_set('session.use_only_cookies', 1);

        session_start();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        ?>
        <form action="process.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="content" id="main-content">
                <div class="header" style="flex-direction: row;">
                    <div class="logo-fest">
                        <span class="lightbulb"></span>
                        <div class="slogan">
                            <div>Meca-Fest</div>
                            <div>Gear up for meXperience</div>                            
                        </div>
                    </div>
                    <div class="logo">
                        <img src="images/mecafest2logo.png" alt="Mecafest Logo">
                    </div>
                </div>
                <div class="titles">
                    <h2>Meca-Fest 2.0</h2>
                    <h4 id="typing-text" style="letter-spacing: 7px;"></h4>
                </div>
                <div class="features">
                    <button id="start-game-btn" class="feature-text"
                        style="color: white; border: 1px solid white; cursor: pointer;">
                        Click here to start the GAME
                    </button>
                </div>

                <div class="registration-form" id="registration-form">
                    <div class="w-full max-w-3xl bg-gray-800 custom-bg p-8 rounded-xl shadow-lg">
                        <h2 class="text-center text-xl font-bold mb-6 custom-font">Registration Phase</h2>

                        <div class="h-[430px] w-full overflow-y-auto pr-2 mb-6 border border-gray-600 rounded-lg p-4">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="email">Email</label>
                                    <input type="email" name="email" id="email"
                                        class="w-full p-2 rounded-lg bg-gray-700 border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="Example@gmail.com" required maxlength="255">
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="team_code">Team Code</label>
                                    <input type="text" name="team_code" id="team_code"
                                        class="w-full p-2 rounded-lg bg-gray-700 border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="Example: XENOX" required maxlength="20">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="first_name">First name</label>
                                    <input type="text" name="first_name" id="first_name"
                                        class="w-full p-2 rounded-lg bg-gray-700 border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="Entered text" required maxlength="100">
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="last_name">Last name</label>
                                    <input type="text" name="last_name" id="last_name"
                                        class="w-full p-2 rounded-lg bg-gray-700 border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="Entered text" required maxlength="100">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="skills">Skills</label>
                                    <input type="text" name="skills" id="skills"
                                        class="w-full p-2 rounded-lg bg-transparent border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="Entered text" required maxlength="100">
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="phone_number">Phone
                                        number</label>
                                    <input type="tel" name="phone_number" id="phone_number"
                                        class="w-full p-2 rounded-lg bg-gray-700 border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="0555555555" required maxlength="20">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="date_of_birth">Date of
                                        birth</label>
                                    <input type="date" name="date_of_birth" id="date_of_birth"
                                        class="w-full p-2 rounded-lg bg-gray-700 border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="YYYY-MM-DD" required>
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="student_id">Student ID
                                        Number</label>
                                    <input type="text" name="student_id" id="student_id"
                                        class="w-full p-2 rounded-lg bg-gray-700 border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="202011111111" required maxlength="50">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="university">University</label>
                                    <input type="text" name="university" id="university"
                                        class="w-full p-2 rounded-lg bg-gray-700 border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="Entered text" required maxlength="100">
                                </div>
                                <div>
                                    <label class="block mb-1 text-sm custom-font" for="field_of_study">Field of
                                        study</label>
                                    <input type="text" name="field_of_study" id="field_of_study"
                                        class="w-full p-2 rounded-lg bg-gray-700 border border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400 custom-font"
                                        placeholder="Entered text" required maxlength="100">
                                </div>
                            </div>
                        </div>

                        <div style="display:flex; justify-content: space-evenly;">
                            <button id="back-btn" type="button"
                                class="px-6 py-2 bg-transparent border border-white text-white rounded-lg hover:bg-white hover:text-gray-800 transition custom-font">
                                Back
                            </button>
                            <button id="to-upload-section" type="button"
                                class="px-6 py-2 bg-transparent border border-white text-white rounded-lg hover:bg-white hover:text-gray-800 transition custom-font">
                                NEXT
                            </button>
                        </div>
                    </div>
                </div>

                <div class="next-section" id="next-section">
                    <div
                        class="w-full h-full bg-gray-800 custom-bg p-8 rounded-xl shadow-lg flex flex-col justify-center items-center">
                        <h2 class="text-center text-xl font-bold mb-6 custom-font">Terms & Conditions</h2>

                        <div
                            class="conditions-container h-96 w-full overflow-y-auto mb-6 px-4 border border-gray-600 rounded-lg">
                            <div class="py-4">
                                <h3 class="font-bold mb-2 custom-font">Step 1: Fill in the Registration Form</h3>
                                <ul class="list-disc pl-5 mb-4 custom-font">
                                    <li>Complete all required fields with accurate information.</li>
                                    <li>Ensure your email address and phone number are correct for future communication.
                                    </li>
                                    <li>Choose a Team Code if you are part of a team. New members using this code will
                                        be automatically linked to your team.</li>
                                </ul>

                                <h3 class="font-bold mb-2 custom-font">Step 2: Upload Your Documents</h3>
                                <ul class="list-disc pl-5 mb-4 custom-font">
                                    <li>After filling in the form, upload a single PDF file that contains:
                                        <ul class="list-disc pl-5 mt-1">
                                            <li>A clear copy of your student card.</li>
                                            <li>A clear copy of your ID card or passport.</li>
                                        </ul>
                                    </li>
                                    <li>Ensure the file size does not exceed 5MB.</li>
                                    <li>Click the "Upload" button and wait for confirmation.</li>
                                </ul>

                                <h3 class="font-bold mb-2 custom-font">Step 3: Submission and Confirmation</h3>
                                <ul class="list-disc pl-5 mb-4 custom-font">
                                    <li>Once your form and PDF file are successfully submitted, you will receive an
                                        on-screen confirmation message.</li>
                                    <li>You will also receive a confirmation email with your registration details.</li>
                                </ul>

                                <h3 class="font-bold mb-2 custom-font">Step 4: Team Registration</h3>
                                <ul class="list-disc pl-5 mb-4 custom-font">
                                    <li>If you have entered a Team Code, your registration will be linked to that team.
                                    </li>
                                    <li>If you are the first member of a new team, this code will be assigned to your
                                        team for others to use (All members must register with the same Team code)</li>
                                </ul>

                                <h3 class="font-bold mb-2 custom-font">Step 5: Selection Notification</h3>
                                <ul class="list-disc pl-5 mb-4 custom-font">
                                    <li>After the registration deadline, all applications will be reviewed.</li>
                                    <li>If you are selected, you will receive a selection email with further
                                        instructions.</li>
                                    <li>If you do not receive a selection email, your application was not approved this
                                        time.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="button-container" style="display: flex; width: 70%; justify-content: space-around">
                            <button id="next-btn" type="button"
                                class="px-6 py-2 bg-transparent border border-white text-white rounded-lg hover:bg-white hover:text-gray-800 transition custom-font">
                                Next
                            </button>
                        </div>
                    </div>
                </div>

                <div class="next-section" id="upload-section">
                    <div class="w-full h-full bg-gray-800 custom-bg p-8 rounded-xl shadow-lg flex flex-col justify-center items-center"
                        style="border: 2px dashed;">
                        <h2 class="text-center text-xl font-bold mb-6 custom-font">Uploading Phase</h2>

                        <div class="upload-container mb-6">
                            <div class="upload-box flex flex-col items-center">
                                <div class="upload-icon mb-4" onclick="document.getElementById('file-upload').click();">
                                    <img src="images/Upload Illustration.png" alt="Upload Icon" class="upload-image">
                                </div>
                                <div class="upload-text text-center mb-4">

                                    <input type="file" name="document" id="file-upload" style="display: none;"
                                        accept=".pdf,.doc,.docx">
                                    <p class="primary-text custom-font mb-2">Student & ID Cards</p>
                                    <p class="secondary-text text-gray-400 custom-font text-xs">All Documents in one
                                        file!</p>
                                </div>
                                <div class="divider flex items-center w-full mb-4">
                                    <div class="line flex-grow h-px bg-gray-600"></div>
                                    <span class="px-4 text-gray-400 custom-font">or</span>
                                    <div class="line flex-grow h-px bg-gray-600"></div>
                                </div>
                                <p class="drive-text custom-font mb-4">Paste a Google Drive link!</p>
                                <div class="link-input-container flex w-full">
                                    <input type="text" name="drive_link" id="drive_link" placeholder="Google Drive link"
                                        class="drive-link-input flex-grow p-2 bg-gray-700 border border-blue-500 rounded-l-lg text-white custom-font"
                                        maxlength="255">
                                    <button type="button" class="upload-button bg-blue-600 p-2 rounded-r-lg">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7 17L17 7M17 7H7M17 7V17" stroke="white" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="button-container" style="display: flex; width: 70%; justify-content: space-around">
                            <button id="previous-btn" type="button"
                                class="px-6 py-2 bg-transparent border border-white text-white rounded-lg hover:bg-white hover:text-gray-800 transition custom-font">
                                Back
                            </button>
                            <button id="register-btn" type="submit" name="register-btn"
                                class="px-6 py-2 bg-transparent border border-white text-white rounded-lg hover:bg-white hover:text-gray-800 transition custom-font">
                                Register
                            </button>
                        </div>
                    </div>
                </div>

                <div class="footer">
                    <div class="tagline">we are waiting for you</div>
                    <div class="partners">
                        <span>MecaClub</span>
                        <span>USTHB</span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="script.js"></script>
</body>

</html>