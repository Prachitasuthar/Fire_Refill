<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>

    <meta charset="utf-8" />
    <title>Login and Registration Form in HTML | GFG</title>
    <!-- <link rel="stylesheet" href="style.css" /> -->
    <meta name="viewport" content="width=device-width, 
                                   initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <style>
        @import url("https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        html,
        body {
            display: grid;
            height: 100%;
            width: 100%;
            place-items: center;
            background: url('{{ session('bg_image', 'default-bg.jpg') }}') no-repeat center center/cover;
            /* opacity: ; */
            transition: background 0.5s ease-in-out;
        }

        .wrapper {
            overflow: hidden;
            max-width: 350px;
            /* border: 1px solid black; */
            background: transparent;
            /* opacity:  !important; */
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.1);
        }

        .wrapper .title-text {
            display: flex;
            width: 200%;
        }

        .wrapper .title {
            width: 50%;
            font-size: 30px;
            font-family: "Times New Roman", Times, serif;
            color: red;
            font-weight: 600;
            text-align: center;
            transition: all 0.6s;
        }

        .wrapper .slide-controls {
            position: relative;
            display: flex;
            border-radius: 10px;
            overflow: hidden;
            margin: 30px 0 10px 0;
            justify-content: center;
            border: 1px solid black;
        }

        .slide-controls .slide {
            height: 100%;
            width: 100%;
            color: red;
            font-size: 18px;
            font-weight: 500;
            text-align: center;
            line-height: 48px;
            cursor: pointer;
            z-index: 1;
            transition: all 0.6s ease;
        }

        .slide-controls label.signup {
            color: #000;
        }

        .slide-controls .slider-tab {
            position: absolute;
            height: 100%;
            width: 50%;
            left: 0;
            z-index: 0;
            border-radius: 5px;
            background: rgb(225, 220, 217);
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        input[type="radio"] {
            display: none;
        }

        #signup:checked~.slider-tab {
            left: 50%;
        }

        #signup:checked~label.signup {
            color: red;
            cursor: default;
            user-select: none;
        }

        #signup:checked~label.login {
            color: #fff;
        }

        #login:checked~label.signup {
            color: #fff;
        }

        #login:checked~label.login {
            cursor: default;
            user-select: none;
        }

        .wrapper .form-container {
            width: 100%;
            overflow: hidden;
        }

        .form-container .form-inner {
            display: flex;
            width: 200%;
        }



        .form-container .form-inner form {
            width: 50%;
            transition: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .form-inner form .field {
            color: white !important;
            height: 50px;
            width: 100%;
            margin-top: 20px;
        }

        .form-inner form .field input {

            height: 100%;
            width: 100%;
            outline: none;
            border-radius: 18px;
            padding-left: 10px;
            border: 1px solid lightgrey;
            border-bottom-width: 2px;
            transition: all 0.3s ease;
        }

        .form-inner form .field input:focus {
            border-color: red;
        }

        .form-inner form .field input::placeholder {
            color: white;
            transition: all 0.3s ease;
        }

        form .field input:focus::placeholder {
            color: #b3b3b3;
        }

        .form-inner form .pass-link {
            margin-top: 5px;
        }

        .form-inner form .signup-link {
            text-align: center;
            margin-top: 30px;
        }

        form .btn input[type="submit"] {
            height: 80%;
            z-index: 1;
            position: relative;
            background: none;
            border: none;
            border: 1px solid white;
            padding-left: 0;
            width: 40%;
            margin-left: 80px;
            border-radius: 10px;
            color: white;
            background-color: rgb(241, 37, 37);
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }

        #verification-success-page {
            display: none;
            background-color: white;
            color: black;
            padding: 20px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        /* Loader Styling */
        #loader-wrapper {
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;
            /* Initially hidden */
        }

        #loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>



</head>

<body>

    <div id="verification-success-page" style="display: none;">
        <h2>Thank you for verifying your email!</h2>
        <p>Your email has been successfully verified. You can now proceed to your <a
                href="{{ route('provider.dashboard') }}" id="provider-dashboard-link">Provider Dashboard</a>.</p>
    </div>


    <div class="wrapper">
        <div class="title-text">
            <div class="title login">Login</div>
            <div class="title signup">Register</div>
            <br /><br />
        </div>
        <div class="form-container">
            <div class="slide-controls">
                <input type="radio" name="slide" id="login" checked />
                <input type="radio" name="slide" id="signup" />
                <label for="login" class="slide login">Sign In</label>
                <label for="signup" class="slide signup">Sign up</label>
                <div class="slider-tab"></div>
            </div>
            <div class="form-inner">

                <form action="{{ route('authenticate') }}" method="POST" class="login" autocomplete="off">
                    @csrf
                    <div class="field">
                        <label for=""><strong>Email Address</strong></label>
                        <input type="text" name="email" placeholder="Enter your email" />
                    </div>
                    <br />
                    <div class="field">
                        <label><strong>Password</strong></label>
                        <input type="password" name="password" placeholder="Enter your password" required />
                    </div>
                    <br />
                    <div class="field btn">
                        <input type="submit" value="Login" />
                    </div>
                    <div class="form signup-link">
                        <a href="#" id="register-as-provider" style="text-decoration: none; color:white;">Register
                            as a Provider</a>
                    </div>
                </form>


                <form action="{{ route('authregister') }}" method="POST" class="signup" id="signup-form"
                    autocomplete="off">
                    @csrf
                    <div class="field">
                        <label for="first_name"><strong>First Name</strong></label>
                        <input type="text" name="first_name" placeholder="Enter First Name" />
                    </div>
                    <br />
                    <div class="field">
                        <label for="last_name"><strong>Last Name</strong></label>
                        <input type="text" name="last_name" placeholder="Enter Last Name" />
                    </div>
                    <br />
                    <div class="field">
                        <label for="mobile_no"><strong>Mobile No.</strong></label>
                        <input type="tel" name="mobile_no" inputmode="numeric" pattern="[0-9]*" maxlength="10" />
                    </div>
                    <br />
                    <div class="field">
                        <label for="address"><strong>Address</strong></label>
                        <input type="text" name="address" placeholder="Enter your address" />
                    </div>
                    <br />
                    <div class="field">
                        <label for="email"><strong>Email</strong></label>
                        <input type="email" name="email" placeholder="Enter your email" />
                    </div>
                    <br />
                    <div class="field">
                        <label for="password"><strong>Password</strong></label>
                        <input type="password" name="password" placeholder="Enter your password" />
                    </div>
                    <br />
                    <input type="hidden" name="user_type" value="user">
                    <br />

                    <!-- Business Name Field (Hidden by Default) -->
                    <div class="field provider-only" style="display: none;">
                        <label for="business_name"><strong>Business Name</strong></label>
                        <input type="text" name="business_name" placeholder="Enter Business Name" />
                    </div>
                    <br />

                    <!-- License Upload Field (Hidden by Default) -->
                    <div class="field provider-only" style="display: none;">
                        <label for="license"><strong>License (Image/PDF)</strong></label>
                        <input type="file" name="license" accept=".jpg,.jpeg,.png,.pdf" />
                    </div>
                    <br />



                    <div class="field btn">
                        <input type="submit" value="Signup" />
                    </div>

                    <div class="form signup-link" id="admin-login-link" style="display:none; color:red;">
                        <a href="#" id="admin-login" style="text-decoration: none; color:white;">Admin
                            Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loader (Initially Hidden) -->
    <div id="loader-wrapper">
        <div id="loader"></div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        </script>
    @endif

    
    <script>
        const loginText = document.querySelector(".title-text .login");
        const loginForm = document.querySelector("form.login");
        const loginBtn = document.querySelector("label.login");
        const signupBtn = document.querySelector("label.signup");
        const signupForm = document.querySelector("form.signup");
        const signupEmail = document.querySelector("form.signup input[name='email']");
        const signupPassword = document.querySelector("form.signup input[name='password']");
        const loginEmail = document.querySelector("form.login input[name='email']");
        const loginPassword = document.querySelector("form.login input[name='password']");
        const adminLoginLink = document.getElementById("admin-login-link");
        const registerAsProviderLink = document.getElementById("register-as-provider");
        const userType = "{{ session('user_type', 'user') }}"; // Default to user

        let backgrounds = {
            user: "url('{{ asset('img/firecarousel3.jpeg') }}')",
            provider: "url('{{ asset('img/fire_image1.jpg') }}')",
            admin: "url('{{ asset('img/firecarousel3.jpeg') }}')"
        };

        function setBackground(type) {
            document.body.style.background = backgrounds[type] || backgrounds['user'];
            document.body.style.backgroundSize = "cover";
            document.body.style.backgroundRepeat = "no-repeat";
            document.body.style.backgroundPosition = "center";
        }

        document.addEventListener("DOMContentLoaded", function() {
            setBackground(userType);

            document.getElementById("register-as-provider").onclick = function() {
                signupForm.style.display = 'block';
                document.querySelector("input[name='user_type']").value = 'provider';
                signupBtn.click();
                adminLoginLink.style.display = 'block';
                setBackground('provider');

                document.querySelectorAll('.provider-only').forEach(field => {
                    field.style.display = 'block';
                });
            };

            document.getElementById("admin-login").onclick = function() {
                signupForm.style.display = 'none';
                loginForm.style.marginLeft = "0%";
                loginText.style.marginLeft = "0%";
                adminLoginLink.style.display = 'none';

                signupBtn.disabled = true;
                loginBtn.disabled = false;

                loginEmail.value = '';
                loginPassword.value = '';

                setBackground('admin');
            };
        });

        signupBtn.onclick = () => {
            loginForm.style.marginLeft = "-50%";
            loginText.style.marginLeft = "-50%";
            adminLoginLink.style.display = 'none';
        };

        loginBtn.onclick = () => {
            loginForm.style.marginLeft = "0%";
            loginText.style.marginLeft = "0%";
            adminLoginLink.style.display = 'none';
        };

        signupForm.addEventListener("submit", async function(e) {
            e.preventDefault();

            const first_name = signupForm.querySelector("input[name='first_name']").value;
            const last_name = signupForm.querySelector("input[name='last_name']").value;
            const email = signupEmail.value;
            const password = signupPassword.value;
            const mobile_no = signupForm.querySelector("input[name='mobile_no']").value;
            const address = signupForm.querySelector("input[name='address']").value;
            const user_type = signupForm.querySelector("input[name='user_type']").value;

            const business_name = document.querySelector("input[name='business_name']")?.value || '';
            const license = document.querySelector("input[name='license']")?.files[0] || null;

            const formData = new FormData();
            formData.append('first_name', first_name);
            formData.append('last_name', last_name);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('password_confirmation', password);
            formData.append('mobile_no', mobile_no);
            formData.append('address', address);
            formData.append('user_type', user_type);

            if (user_type === 'provider') {
                formData.append('business_name', business_name);
                if (license) {
                    formData.append('license', license);
                }
            }

            document.getElementById("loader-wrapper").style.display = "flex";

            try {
                const response = await fetch("{{ route('authregister') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                            "content"),
                    },
                    body: formData,
                });

                const result = await response.json();
                document.getElementById("loader-wrapper").style.display = "none";

                if (response.ok) {
                    // ✅ SUCCESS: Show success message and redirect
                    Swal.fire({
                        title: "Success!",
                        text: "Verification email has been sent. Please verify your email before logging in.",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        if (user_type === 'provider') {
                            window.location.href = "{{ route('provider.login') }}";
                        } else {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                } else {
                   
                    let errorMessage = "Signup failed. Please try again.";

                    if (result.errors) {
                        errorMessage = Object.values(result.errors).flat().join("\n");
                    } else if (result.message && result.message.includes("taken")) {
                        errorMessage = "Email already exists. Please login.";
                    }

                    Swal.fire({
                        title: "Error!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            } catch (error) {
                console.error("Signup error:", error);
                document.getElementById("loader-wrapper").style.display = "none";

                Swal.fire({
                    title: "Error!",
                    text: "An error occurred. Please try again.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });


        document.addEventListener("DOMContentLoaded", function() {
            if (registerAsProviderLink && window.location.href.includes('provider/login')) {
                registerAsProviderLink.style.display = 'none';
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("signup-form");

            const inputs = document.querySelectorAll("#signup-form input");
            const emailInput = document.querySelector("input[name='email']");
            const mobileInput = document.querySelector("input[name='mobile_no']");

            function showError(input, message) {
                let errorDiv = input.nextElementSibling;
                if (!errorDiv || !errorDiv.classList.contains("error-message")) {
                    errorDiv = document.createElement("div");
                    errorDiv.className = "error-message";
                    errorDiv.style.color = "red";
                    input.parentNode.insertBefore(errorDiv, input.nextSibling);
                }
                errorDiv.innerText = message;
            }

            function clearError(input) {
                let errorDiv = input.nextElementSibling;
                if (errorDiv && errorDiv.classList.contains("error-message")) {
                    errorDiv.innerText = "";
                }
            }

            function validateRequired(input) {
                if (input.value.trim() === "") {
                    showError(input, "This field is required.");
                    return false;
                }
                clearError(input);
                return true;
            }

            function validateMobile() {
                const mobilePattern = /^[0-9]{10}$/;
                if (!mobilePattern.test(mobileInput.value)) {
                    showError(mobileInput, "Enter a valid 10-digit mobile.");
                    return false;
                }
                clearError(mobileInput);
                return true;
            }

            function validateEmail() {
                const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (!emailPattern.test(emailInput.value)) {
                    showError(emailInput, "Enter a valid email address.");
                    return false;
                }
                clearError(emailInput);
                checkEmailExists(emailInput.value);
                return true;
            }

            function validatePassword(input) {
                if (input.value.length < 6) {
                    showError(input, "Password must be at least 6 characters.");
                    return false;
                }
                clearError(input);
                return true;
            }

            function checkEmailExists(emailValue) {
                fetch("{{ route('checkEmailExists') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector("input[name='_token']").value,
                        },
                        body: JSON.stringify({
                            email: emailValue
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            showError(emailInput, "This email is already registered.");
                        } else {
                            clearError(emailInput);
                        }
                    })
                    .catch(error => console.error("Error:", error));
            }

            inputs.forEach(input => {
                input.addEventListener("input", function() {
                    if (input.name === "email") {
                        validateEmail();
                    } else if (input.name === "mobile_no") {
                        validateMobile();
                    } else if (input.name === "password") {
                        validatePassword(input);
                    } else {
                        validateRequired(input);
                    }
                });
            });

            form.addEventListener("submit", function(e) {
                let isValid = true;
                inputs.forEach(input => {
                    if (input.name === "email" && !validateEmail()) isValid = false;
                    if (input.name === "mobile_no" && !validateMobile()) isValid = false;
                    if (input.name === "password" && !validatePassword(input)) isValid = false;
                    if (!validateRequired(input)) isValid = false;
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>




</body>



</html>
