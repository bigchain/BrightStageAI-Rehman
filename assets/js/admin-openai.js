/*
 * Admin OpenAI AJAX Handler
 * This script handles the AJAX request for generating text using the OpenAI API
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('brightsideai-generate-text-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const promptField = document.getElementById('brightsideai_prompt');
        const outputField = document.getElementById('brightsideai-generated-text');

        const prompt = promptField ? promptField.value : '';

        // Clear previous text and show loading message
        if (outputField) {
            outputField.value = 'Generating text...';
        }

        // Prepare form data for AJAX
        const formData = new URLSearchParams();
        formData.append('action', 'brightsideai_generate_text');
        formData.append('prompt', prompt);
        formData.append('nonce', brightsideaiAdmin.nonce);

        fetch(brightsideaiAdmin.ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            console.log('Admin OpenAI response:', data);
            if (data.success) {
                outputField.value = data.data;
            } else {
                outputField.value = 'Error: ' + (data.data ? data.data : JSON.stringify(data));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            outputField.value = 'Error: ' + error;
        });
    });
});
