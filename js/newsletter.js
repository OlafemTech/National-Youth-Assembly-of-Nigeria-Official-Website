/**
 * Newsletter Subscription Handling
 * 
 * This script handles the newsletter subscription form submissions across all pages.
 * It includes CSRF protection, validation, and AJAX submission.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Find all newsletter forms on the page
    const newsletterForms = document.querySelectorAll('.newsletter-form');
    
    // Fetch CSRF token
    fetchCsrfToken();
    
    // Add event listeners to all newsletter forms
    newsletterForms.forEach(form => {
        setupNewsletterForm(form);
    });
});

/**
 * Fetch CSRF token from the server
 */
function fetchCsrfToken() {
    fetch('backend/get_csrf_token.php')
        .then(response => response.json())
        .then(data => {
            if (data.csrf_token) {
                // Store token in localStorage for use across the site
                localStorage.setItem('csrf_token', data.csrf_token);
            }
        })
        .catch(error => {
            console.error('Error fetching CSRF token:', error);
        });
}

/**
 * Set up newsletter form with validation and submission handling
 * 
 * @param {HTMLFormElement} form The newsletter form element
 */
function setupNewsletterForm(form) {
    // Add hidden CSRF token field
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.className = 'csrf-token';
    form.appendChild(csrfInput);
    
    // Add hidden source page field
    const sourceInput = document.createElement('input');
    sourceInput.type = 'hidden';
    sourceInput.name = 'source_page';
    sourceInput.value = window.location.pathname;
    form.appendChild(sourceInput);
    
    // Create container for success/error messages
    const messageContainer = document.createElement('div');
    messageContainer.className = 'newsletter-message';
    messageContainer.style.display = 'none';
    messageContainer.style.marginTop = '10px';
    messageContainer.style.padding = '8px 12px';
    messageContainer.style.borderRadius = '4px';
    form.appendChild(messageContainer);
    
    // reCAPTCHA implementation has been removed
    
    // Handle form submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Get the email input
        const emailInput = form.querySelector('.newsletter-input');
        
        // Update CSRF token from localStorage
        const csrfToken = localStorage.getItem('csrf_token');
        if (csrfToken) {
            form.querySelector('.csrf-token').value = csrfToken;
        }
        
        // Basic validation
        if (!emailInput.value.trim()) {
            showMessage(messageContainer, 'Please enter your email address.', 'error');
            return;
        }
        
        if (!isValidEmail(emailInput.value.trim())) {
            showMessage(messageContainer, 'Please enter a valid email address.', 'error');
            return;
        }
        
        // reCAPTCHA validation has been removed
        
        // Disable submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Subscribing...';
        
        // Prepare form data
        const formData = new FormData(form);
        
        // Send AJAX request
        fetch('backend/process_newsletter.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
            
            // reCAPTCHA reset code has been removed
            
            if (data.success) {
                // Show success message
                showMessage(messageContainer, data.message, 'success');
                
                // Reset form
                form.reset();
            } else {
                // Show error message
                showMessage(messageContainer, data.message, 'error');
            }
        })
        .catch(error => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
            
            // Show error message
            showMessage(messageContainer, 'An error occurred. Please try again later.', 'error');
            console.error('Error:', error);
        });
    });
}

/**
 * Show message in the container
 * 
 * @param {HTMLElement} container The message container
 * @param {string} message The message to display
 * @param {string} type The message type ('success' or 'error')
 */
function showMessage(container, message, type) {
    container.textContent = message;
    container.style.display = 'block';
    
    if (type === 'success') {
        container.style.backgroundColor = '#d4edda';
        container.style.color = '#155724';
        container.style.border = '1px solid #c3e6cb';
    } else {
        container.style.backgroundColor = '#f8d7da';
        container.style.color = '#721c24';
        container.style.border = '1px solid #f5c6cb';
    }
    
    // Hide message after 5 seconds
    setTimeout(() => {
        container.style.display = 'none';
    }, 5000);
}

/**
 * Validate email format
 * 
 * @param {string} email The email to validate
 * @return {boolean} True if valid, false otherwise
 */
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}
