document.addEventListener('alpine:init', () => {
    // Add this before your Alpine.data definition
    console.log('Alpine initializing...');
    
    Alpine.data('brightsideApp', () => {
        console.log('BrightsideApp component initializing...'); // Debug point 2
        
        return {
        projects: [],
        currentProject: null,
        view: 'dashboard',
        isGenerating: false,
        showNewProjectModal: false,
        showBuyCreditsModal: false,
        newProjectName: '',
        newProjectDescription: '',
        selectedVoice: 'emma',
        selectedTemplate: 'modern',
        webinarDescription: '',
        webinarDuration: 30,
        slideContent: '',
        narrationText: '',
        activeMarketingTab: 'press-releases',
        toastVisible: false,
        showEditProjectModal: false,
        selectedPackage: '100',
        isPurchasing: false,  // Add this to track purchase state
        isSaving: false,
        isEnhancing: false,
        enhancedDescription: '',
        webinarDescription: '',
        webinarDuration: 30,
        slideContent: '',
        narrationText: '',
        scriptBuilderStep: 'editor',
        credits: localStorage.getItem('brightSideAICredits') ? 
            parseInt(localStorage.getItem('brightSideAICredits')) : 100,  // Initialize from localStorage
        editingProject: {
            id: null,
            name: '',
            shortDescription: ''
        },
        // Voice options
        voiceOptions: [
            { id: 'emma', name: 'Emma', description: 'Friendly and professional female voice' },
            { id: 'james', name: 'James', description: 'Authoritative male voice' },
            { id: 'sarah', name: 'Sarah', description: 'Engaging and energetic female voice' }
        ],

        // Template options
        templateOptions: [
            { id: 'modern', name: 'Modern Minimal', description: 'Clean and contemporary design' },
            { id: 'corporate', name: 'Corporate Professional', description: 'Traditional business style' },
            { id: 'creative', name: 'Creative Impact', description: 'Bold and dynamic design' }
        ],

        init() {
            // Your existing project loading code
            const savedProjects = localStorage.getItem('brightSideAIProjects');
            if (savedProjects) {
                this.projects = JSON.parse(savedProjects);
            }
        
            // Add this: Load saved credits
            const savedCredits = localStorage.getItem('brightSideAICredits');
            if (savedCredits) {
                this.credits = parseInt(savedCredits);
            }
        
            // Your existing project watch
            this.$watch('projects', (value) => {
                localStorage.setItem('brightSideAIProjects', JSON.stringify(value));
            });
        
            // Add this: Watch credits changes
            this.$watch('credits', (value) => {
                localStorage.setItem('brightSideAICredits', value.toString());
            });
        },
        
        // Project Management Methods
        createProject() {
            if (!this.newProjectName || !this.newProjectDescription) return;
        
            const newProject = {
                id: Date.now().toString(),
                name: this.newProjectName,
                shortDescription: this.newProjectDescription,
                detailedDescription: '',
                duration: 30,
                creditsUsed: 0,
                progress: 0,
                script: '', // Add this
                narration: '', // Add this
                promoPack: {
                    pressRelease: '',
                    socialPosts: '',
                    emailSequence: '',
                    miniEbook: ''
                },
                assets: [
                    { type: 'script', content: '', status: 'not started' },
                    { type: 'slides', content: '', status: 'not started' },
                    { type: 'pitch', content: '', status: 'not started' },
                    { type: 'emailSequence', content: '', status: 'not started' },
                    { type: 'socialPosts', content: '', status: 'not started' }
                ],
                createdAt: new Date(),
                archived: false
            };
        
            this.projects.push(newProject);
            this.showNewProjectModal = false;
            this.newProjectName = '';
            this.newProjectDescription = '';
            this.showToast('Project Created', 'Your new project has been created successfully.');
        },

        setCurrentProject(project) {
            this.currentProject = project;
            this.view = 'project';
            // Load all saved content
            this.webinarDescription = project.detailedDescription || '';
            this.webinarDuration = project.duration || 30;
            this.enhancedDescription = project.detailedDescription || ''; // Add this
            this.slideContent = project.script || ''; // Add this
            this.narrationText = project.narration || ''; // Add this
        },

        updateProject(project) {
            const index = this.projects.findIndex(p => p.id === project.id);
            if (index !== -1) {
                this.projects[index] = project;
                this.currentProject = project;
                // Force update localStorage
                localStorage.setItem('brightSideAIProjects', JSON.stringify(this.projects));
            }
        },

        deleteProject(project) {
            this.projects = this.projects.filter(p => p.id !== project.id);
            if (this.currentProject?.id === project.id) {
                this.currentProject = null;
                this.view = 'dashboard';
            }
        },
        editProject(project) {
            // Create a copy of the project to edit
            this.editingProject = {
                id: project.id,
                name: project.name,
                shortDescription: project.shortDescription
            };
            
            // Use Flowbite's modal
            const modal = document.getElementById('edit-project-modal');
            if (modal) {
                const modalInstance = new Modal(modal);
                modalInstance.show();
            }
        },

        saveProjectChanges() {
            if (!this.editingProject.name || !this.editingProject.shortDescription) return;
        
            // Find and update the project
            const projectToUpdate = this.projects.find(p => p.id === this.editingProject.id);
            if (projectToUpdate) {
                projectToUpdate.name = this.editingProject.name;
                projectToUpdate.shortDescription = this.editingProject.shortDescription;
                
                if (this.currentProject?.id === projectToUpdate.id) {
                    this.currentProject = {...projectToUpdate};
                }
        
                this.updateProject(projectToUpdate);
                
                // Close modal using Flowbite
                const modal = document.getElementById('edit-project-modal');
                if (modal) {
                    const modalInstance = new Modal(modal);
                    modalInstance.hide();
                }
                
                this.showToast('Project Updated', 'Your project has been updated successfully.');
                
                this.editingProject = {
                    id: null,
                    name: '',
                    shortDescription: ''
                };
            }
        },
        closeEditModal() {
            const modal = document.getElementById('edit-project-modal');
            if (modal) {
                const modalInstance = new Modal(modal);
                modalInstance.hide();
                // Reset form
                this.editingProject = {
                    id: null,
                    name: '',
                    shortDescription: ''
                };
            }
        },

        // Script Builder Methods
        async enhanceDescription() {
            console.log('Enhancing description...'); // Debug log
            if (!this.webinarDescription || this.isEnhancing) {
                console.log('No description or already enhancing'); // Debug log
                return;
            }
            
            this.isEnhancing = true;
            this.error = null;

            try {
                console.log('Making API request...'); // Debug log
                const formData = new URLSearchParams();
                formData.append('action', 'brightsideai_generate_text');
                formData.append('prompt', this.webinarDescription);
                formData.append('type', 'enhance');
                formData.append('nonce', brightsideaiConfig.nonce);

                console.log('Request data:', {
                    url: brightsideaiConfig.ajaxUrl,
                    nonce: brightsideaiConfig.nonce,
                    prompt: this.webinarDescription
                }); // Debug log

                const response = await fetch(brightsideaiConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: formData.toString()
                });

                const data = await response.json();
                console.log('API response:', data); // Debug log
                
                if (data.success) {
                    this.enhancedDescription = data.data;
                    this.webinarDescription = data.data; // Update the textarea with enhanced content
                    this.showToast('Success', 'Description enhanced successfully!', 'success');
                } else {
                    this.error = data.data || 'Error enhancing description';
                    this.showToast('Error', this.error, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.error = 'Failed to enhance description. Please try again.';
                this.showToast('Error', this.error, 'error');
            } finally {
                this.isEnhancing = false;
            }
        },

        async generateScript() {
            if (!this.enhancedDescription) {
                this.showToast('Enhancement Required', 'Please enhance your description with AI first', 'error');
                return;
            }
        
            this.isGenerating = true;
            try {
                // Simulate script generation
                await new Promise(resolve => setTimeout(resolve, 3000));
                
                const generatedSlides = `# Introduction
        - Welcome to Digital Marketing Innovation 2024
        - About the presenter
        - What you'll learn today
        
        # The Evolution of Digital Marketing
        - Traditional vs Modern Approaches
        - Key Industry Trends
        - Impact of AI and Automation
        
        # Data-Driven Decision Making
        - Understanding Your Analytics
        - Key Metrics to Track
        - Creating Actionable Insights`;
        
                const generatedNarration = `Welcome to our webinar on Digital Marketing Innovation 2024! I'm excited to guide you through the latest strategies and technologies that are reshaping the marketing landscape.
        
        In this session, we'll explore how data-driven decision making and AI-powered automation are revolutionizing digital marketing.`;
        
                this.slideContent = generatedSlides;
                this.narrationText = generatedNarration;
                
                if (this.currentProject) {
                    this.currentProject.script = generatedSlides;
                    this.currentProject.narration = generatedNarration;
                    this.currentProject.progress = Math.min(this.currentProject.progress + 20, 100);
                    this.updateProject(this.currentProject);
                }
                
                this.showToast('Script Generated', 'Your webinar script has been generated successfully.');
            } catch (error) {
                this.showToast('Generation Failed', 'Failed to generate script. Please try again.', 'error');
            } finally {
                this.isGenerating = false;
            }
        },
        async saveChanges() {
            if (!this.slideContent || !this.narrationText) {
                this.showToast('Content Required', 'Please generate or enter both slide content and narration', 'error');
                return;
            }
        
            this.isSaving = true;
            try {
                if (this.currentProject) {
                    const updatedProject = {
                        ...this.currentProject,
                        script: this.slideContent,
                        narration: this.narrationText,
                        detailedDescription: this.webinarDescription,
                        duration: this.webinarDuration,
                        assets: this.currentProject.assets.map(asset =>
                            asset.type === 'script' 
                                ? { ...asset, content: this.slideContent, status: 'completed' } 
                                : asset
                        ),
                        progress: Math.min(this.currentProject.progress + 20, 100)
                    };
                    
                    this.updateProject(updatedProject);
                    
                    // Force data persistence
                    localStorage.setItem('brightSideAIProjects', JSON.stringify(this.projects));
                    
                    this.showToast('Changes Saved', 'Your script has been saved successfully');
                }
            } catch (error) {
                console.error('Save error:', error);
                this.showToast('Save Failed', 'Failed to save changes. Please try again.', 'error');
            } finally {
                this.isSaving = false;
            }
        },
        

        // Marketing Suite Methods
        async generateMarketingContent(type) {
            this.isGenerating = true;
            try {
                // Simulate content generation
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                const content = `Generated ${type} content`;
                if (this.currentProject) {
                    this.currentProject.assets = this.currentProject.assets.map(asset => 
                        asset.type === type ? { ...asset, content, status: 'completed' } : asset
                    );
                    this.currentProject.progress = Math.min(this.currentProject.progress + 10, 100);
                    this.updateProject(this.currentProject);
                }
                
                this.showToast('Content Generated', `Your ${type} has been generated successfully.`);
            } catch (error) {
                this.showToast('Generation Failed', 'Failed to generate content. Please try again.', 'error');
            } finally {
                this.isGenerating = false;
            }
        },

        navigateToSlides() {
            if (!this.currentProject?.script) {
                this.showToast('Content Required', 'Please generate and save your script first', 'error');
                return;
            }
            this.view = 'builder';
            this.scriptBuilderStep = 'slides';
        },

        // Utility Methods
        showToast(title, message, type = 'success') {
            console.log('showToast called:', { title, message, type }); // Debug point 3
            console.log(`${type}: ${title} - ${message}`);
            
            // Change this.showToast to this.toastVisible
            this.toastVisible = true;
            setTimeout(() => {
                this.toastVisible = false;
            }, 3000);
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString();
        },
        selectPackage(amount) {
            this.selectedPackage = amount;
        },
        async purchaseCredits(package) {
            try {
                if (this.isPurchasing) return; // Prevent double-clicks
                this.isPurchasing = true;
        
                // Convert package to number
                const amount = parseInt(package);
                
                // Simulate API call to payment processor
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                // Add credits based on package selected
                this.credits += amount;
                
                // Hide the modal
                const modal = document.getElementById('buy-credits-modal');
                if (modal) {
                    const modalInstance = new Modal(modal);
                    modalInstance.hide();
                }
                
                // Show success message
                this.showToast('Credits Purchased', `Successfully purchased ${amount} credits.`);
                
                // Reset selected package
                this.selectedPackage = '100';
            } catch (error) {
                this.showToast('Purchase Failed', 'Failed to complete the purchase. Please try again.', 'error');
                console.error('Purchase error:', error);
            } finally {
                this.isPurchasing = false;
            }
        },
        //slides
        slides: [],
        selectedVoice: 'emma',
        selectedTemplate: 'modern',
        headshot: null,
        isGeneratingVideo: false,
        videoProgress: 0,
        isPlayingPreview: false,
        showVideoDialog: false,
        // Add voice and template options as constants
        voiceOptions: [
            { id: 'emma', name: 'Emma', description: 'Friendly and professional female voice' },
            { id: 'james', name: 'James', description: 'Authoritative male voice' },
            { id: 'sarah', name: 'Sarah', description: 'Engaging and energetic female voice' }
        ],
        templateOptions: [
            {
                id: 'modern',
                name: 'Modern Minimal',
                description: 'Clean and contemporary design with ample white space',
                preview: '/placeholder.svg'
            },
            {
                id: 'corporate',
                name: 'Corporate Professional',
                description: 'Traditional business style with structured layouts',
                preview: '/placeholder.svg'
            },
            {
                id: 'creative',
                name: 'Creative Impact',
                description: 'Bold and dynamic design for engaging presentations',
                preview: '/placeholder.svg'
            }
        ],
    }});
});