document.addEventListener('DOMContentLoaded', function () {

    
    // COMMON VALIDATION FUNCTIONS
    
    function createErrorElement(inputElement) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#d4a5a5';
        errorDiv.style.fontSize = '0.85rem';
        errorDiv.style.marginTop = '0.3rem';
        errorDiv.style.display = 'none';
        inputElement.parentElement.appendChild(errorDiv);
        return errorDiv;
    }

    function validateEmail(email) {
        const hasAt = email.includes('@');
        const hasDot = email.includes('.');
        const atIndex = email.indexOf('@');
        const dotIndex = email.lastIndexOf('.');

        if (!hasAt || !hasDot) {
            return 'Email must contain @ and .';
        }

        if (atIndex <= 0 || dotIndex <= atIndex + 1 || dotIndex === email.length - 1) {
            return 'Invalid email format';
        }

        return null;
    }

    function validatePassword(password) {
        if (password.length < 8) {
            return 'Password must be at least 8 characters';
        }
        if (!/[A-Z]/.test(password)) {
            return 'Password must contain at least one uppercase letter';
        }
        if (!/[a-z]/.test(password)) {
            return 'Password must contain at least one lowercase letter';
        }
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            return 'Password must contain at least one special character';
        }
        return null;
    }

    function showError(errorElement, message) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        errorElement.previousElementSibling.style.borderColor = '#d4a5a5';
    }

    function hideError(errorElement) {
        errorElement.style.display = 'none';
        errorElement.previousElementSibling.style.borderColor = '#e8d5c4';
    }

    // LOGIN FORM VALIDATION

    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        const emailError = createErrorElement(emailInput);
        const passwordError = createErrorElement(passwordInput);

        emailInput.addEventListener('blur', function () {
            const error = validateEmail(this.value);
            error ? showError(emailError, error) : hideError(emailError);
        });

        passwordInput.addEventListener('blur', function () {
            const error = validatePassword(this.value);
            error ? showError(passwordError, error) : hideError(passwordError);
        });

        emailInput.addEventListener('input', () => hideError(emailError));
        passwordInput.addEventListener('input', () => hideError(passwordError));

        loginForm.addEventListener('submit', function (e) {
            const emailErr = validateEmail(emailInput.value);
            const passErr = validatePassword(passwordInput.value);

            let hasErrors = false;

            if (emailErr) {
                showError(emailError, emailErr);
                hasErrors = true;
            }

            if (passErr) {
                showError(passwordError, passErr);
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
            }
        });
    }


    // SIGNUP FORM VALIDATION

    const signupForm = document.getElementById('signupForm');

    if (signupForm) {
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        const emailError = createErrorElement(emailInput);
        const passwordError = createErrorElement(passwordInput);

        emailInput.addEventListener('blur', function () {
            const error = validateEmail(this.value);
            error ? showError(emailError, error) : hideError(emailError);
        });

        passwordInput.addEventListener('blur', function () {
            const error = validatePassword(this.value);
            error ? showError(passwordError, error) : hideError(passwordError);
        });

        emailInput.addEventListener('input', () => hideError(emailError));
        passwordInput.addEventListener('input', () => hideError(passwordError));

        signupForm.addEventListener('submit', function (e) {
            const emailErr = validateEmail(emailInput.value);
            const passErr = validatePassword(passwordInput.value);

            let hasErrors = false;

            if (emailErr) {
                showError(emailError, emailErr);
                hasErrors = true;
            }

            if (passErr) {
                showError(passwordError, passErr);
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
            }
        });
    }

});




// view recipe page js code

let likes = 15;

function likeRecipe() {
    likes++;
    document.getElementById("likeCount").textContent = likes;
}

function addComment() {
    const input = document.getElementById("commentInput");
    const text = input.value.trim();

    if (text === "") {
        alert("Please write a comment first.");
        return;
    }

    const commentDiv = document.createElement("div");
    commentDiv.className = "comment";

    commentDiv.innerHTML = `
        <strong>You:</strong> ${text}
        <div class="comment-time">Now</div>
    `;

    document.getElementById("commentsBox").prepend(commentDiv);
    input.value = "";
}


function formatTime(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffMinutes = Math.floor(diffMs / 60000);

    if (diffMinutes < 1) {
        return "Just now";
    } else if (diffMinutes < 60) {
        return diffMinutes + " minutes ago";
    } else if (date.toDateString() === now.toDateString()) {
        return "Today";
    } else {
        return date.toLocaleDateString();
    }
}

