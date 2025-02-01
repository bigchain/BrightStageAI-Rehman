/*
 * Frontend OpenAI Integration
 * This script handles the frontend AJAX requests for the Script Builder
 */

document.addEventListener('DOMContentLoaded', function() {
    window.brightsideApp = function() {
        return {
            webinarDescription: '',
            enhancedDescription: '',
            slideContent: '',
            narrationText: '',
            webinarDuration: 30,
            isEnhancing: false,
            isGenerating: false,
            error: null,

            // Enhance the webinar description using AI
            async enhanceDescription() {
                if (!this.webinarDescription || this.isEnhancing) return;
                
                this.isEnhancing = true;
                this.error = null;

                try {
                    const formData = new URLSearchParams();
                    formData.append('action', 'brightsideai_generate_text');
                    formData.append('prompt', this.webinarDescription);
                    formData.append('type', 'enhance');
                    formData.append('nonce', brightsideaiFront.nonce);

                    const response = await fetch(brightsideaiFront.ajaxurl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: formData.toString()
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        this.enhancedDescription = data.data;
                        this.webinarDescription = data.data; // Update the textarea with enhanced content
                    } else {
                        this.error = data.data || 'Error enhancing description';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.error = 'Failed to enhance description. Please try again.';
                } finally {
                    this.isEnhancing = false;
                }
            },

            // Generate both slide content and narration
            async generateScript() {
                if (!this.enhancedDescription || this.isGenerating) return;
                
                this.isGenerating = true;
                this.error = null;

                try {
                    // Generate slides
                    const slidesData = new URLSearchParams();
                    slidesData.append('action', 'brightsideai_generate_text');
                    slidesData.append('prompt', this.enhancedDescription);
                    slidesData.append('type', 'slides');
                    slidesData.append('nonce', brightsideaiFront.nonce);

                    // Generate narration
                    const narrationData = new URLSearchParams();
                    narrationData.append('action', 'brightsideai_generate_text');
                    narrationData.append('prompt', this.enhancedDescription);
                    narrationData.append('type', 'narration');
                    narrationData.append('nonce', brightsideaiFront.nonce);

                    // Make both requests in parallel
                    const [slidesResponse, narrationResponse] = await Promise.all([
                        fetch(brightsideaiFront.ajaxurl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                            },
                            body: slidesData.toString()
                        }),
                        fetch(brightsideaiFront.ajaxurl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                            },
                            body: narrationData.toString()
                        })
                    ]);

                    const [slidesResult, narrationResult] = await Promise.all([
                        slidesResponse.json(),
                        narrationResponse.json()
                    ]);

                    if (slidesResult.success && narrationResult.success) {
                        this.slideContent = slidesResult.data;
                        this.narrationText = narrationResult.data;
                    } else {
                        this.error = (slidesResult.data || narrationResult.data || 'Error generating script');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.error = 'Failed to generate script. Please try again.';
                } finally {
                    this.isGenerating = false;
                }
            }
        };
    };
});
