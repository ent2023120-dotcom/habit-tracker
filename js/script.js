// Smooth Scrolling
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let valid = true;
    form.querySelectorAll('input[required]').forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            valid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    return valid;
}

// Progress Bar Animation on Load
window.addEventListener('load', () => {
    document.querySelectorAll('.progress-bar').forEach(bar => {
        const width = bar.getAttribute('data-width');
        bar.style.width = width + '%';
    });
});

// Register Form Validation
function validateRegisterForm() {
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;

    if (username.length < 3) {
        alert('Username must be at least 3 characters!');
        return false;
    }
    if (!email.includes('@')) {
        alert('Please enter a valid email!');
        return false;
    }
    if (password.length < 6) {
        alert('Password must be at least 6 characters!');
        return false;
    }
    if (password !== confirm) {
        alert('Passwords do not match!');
        return false;
    }
    return true;
}

// Login Form Validation
function validateLoginForm() {
    const email = document.getElementById('login_email').value.trim();
    const password = document.getElementById('login_password').value;

    if (!email.includes('@')) {
        alert('Please enter a valid email!');
        return false;
    }
    if (password.length < 1) {
        alert('Please enter your password!');
        return false;
    }
    return true;
}

// Validate Habit Form
function validateHabitForm() {
    const name = document.getElementById('habit_name').value.trim();
    const goal = parseInt(document.getElementById('goal').value);

    if (name.length < 2) {
        alert('Habit name must be at least 2 characters!');
        return false;
    }
    if (isNaN(goal) || goal < 1 || goal > 365) {
        alert('Goal must be between 1 and 365 days!');
        return false;
    }
    return true;
}

// Increment Habit Progress via AJAX
function incrementHabit(habitId) {
    fetch('dashboard.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'increment=1&habit_id=' + habitId
    })
    .then(response => response.json())
    .then(data => {
        const progressEl = document.getElementById('progress-' + habitId);
        const barEl = document.getElementById('bar-' + habitId);
        
        const progress = data.progress;
        const goal = data.goal;
        let percent = Math.round((progress / goal) * 100);
        if (percent > 100) percent = 100;

        progressEl.textContent = progress;
        barEl.style.width = percent + '%';
        barEl.textContent = percent + '%';
        barEl.setAttribute('data-width', percent);

        if (progress >= goal) {
            barEl.classList.remove('bg-success');
            barEl.classList.add('bg-primary');
            barEl.textContent = '🎉 Completed!';
        }
    })
    .catch(error => console.error('Error:', error));
}

// Fade-in animation for habit cards
window.addEventListener('load', () => {
    document.querySelectorAll('.habit-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Contact Form Validation
function validateContactForm() {
    const name = document.getElementById('contact_name').value.trim();
    const email = document.getElementById('contact_email').value.trim();
    const message = document.getElementById('contact_message').value.trim();

    if (name.length < 2) {
        alert('Please enter your name (at least 2 characters).');
        return false;
    }
    if (!email.includes('@') || !email.includes('.')) {
        alert('Please enter a valid email address.');
        return false;
    }
    if (message.length < 10) {
        alert('Message must be at least 10 characters long.');
        return false;
    }
    return true;
}