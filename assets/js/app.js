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
        articleContext: '',
        generatedArticle: '',
        activeMarketingTab: 'articles',
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
        credits: 100,  // Initialize from WordPress database
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
            console.log('Initializing app...'); // Debug log
            this.loadProjects();
        },
        
        async loadProjects() {
            console.log('Loading projects...'); // Debug log
            try {
                const formData = new URLSearchParams();
                formData.append('action', 'brightsideai_get_projects');
                formData.append('nonce', brightsideaiConfig.nonce);

                const response = await fetch(brightsideaiConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: formData.toString()
                });

                const result = await response.json();
                console.log('Projects loaded:', result); // Debug log

                if (result.success) {
                    this.projects = result.data;
                } else {
                    console.error('Failed to load projects:', result.data);
                    this.showToast('Error', 'Failed to load projects. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Load error:', error);
                this.showToast('Error', 'Failed to load projects. Please check your connection.', 'error');
            }
        },

        async saveChanges() {
            console.log('Saving changes...'); // Debug log
            this.isSaving = true;
            
            try {
                if (this.currentProject) {
                    console.log('Current project found:', this.currentProject.id); // Debug log
                    
                    // Update project with all current content
                    const updatedProject = {
                        ...this.currentProject,
                        detailedDescription: this.webinarDescription,
                        enhancedDescription: this.enhancedDescription,
                        script: this.slideContent,
                        narration: this.narrationText,
                        duration: this.webinarDuration,
                        lastModified: new Date(),
                        assets: this.currentProject.assets.map(asset => {
                            switch(asset.type) {
                                case 'script':
                                    return { ...asset, content: this.slideContent, status: this.slideContent ? 'completed' : 'not started' };
                                case 'slides':
                                    return { ...asset, content: this.slideContent, status: this.slideContent ? 'completed' : 'not started' };
                                default:
                                    return asset;
                            }
                        }),
                        progress: this.calculateProgress()
                    };
                    
                    console.log('Saving updated project:', updatedProject); // Debug log

                    const formData = new URLSearchParams();
                    formData.append('action', 'brightsideai_save_project');
                    formData.append('nonce', brightsideaiConfig.nonce);
                    formData.append('project', JSON.stringify(updatedProject));

                    const response = await fetch(brightsideaiConfig.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: formData.toString()
                    });

                    const result = await response.json();
                    console.log('Save response:', result); // Debug log

                    if (result.success) {
                        // Refresh the projects list
                        await this.loadProjects();
                        
                        // Update current project with the latest data
                        const savedProject = this.projects.find(p => p.id === this.currentProject.id);
                        if (savedProject) {
                            this.currentProject = savedProject;
                        }
                        
                        this.showToast('Success', 'Your changes have been saved successfully', 'success');
                    } else {
                        throw new Error(result.data || 'Failed to save project');
                    }
                } else {
                    console.log('No current project found'); // Debug log
                    this.showToast('Error', 'No project selected. Please create or select a project first.', 'error');
                }
            } catch (error) {
                console.error('Save error:', error);
                this.showToast('Error', 'Failed to save changes. Please try again.', 'error');
            } finally {
                this.isSaving = false;
            }
        },

        async deleteProject(project) {
            try {
                const formData = new URLSearchParams();
                formData.append('action', 'brightsideai_delete_project');
                formData.append('nonce', brightsideaiConfig.nonce);
                formData.append('project_id', project.id);

                const response = await fetch(brightsideaiConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: formData.toString()
                });

                const result = await response.json();
                if (result.success) {
                    // Remove from local array
                    this.projects = this.projects.filter(p => p.id !== project.id);
                    
                    if (this.currentProject?.id === project.id) {
                        this.currentProject = null;
                        this.view = 'dashboard';
                    }
                    
                    this.showToast('Success', 'Project deleted successfully', 'success');
                } else {
                    throw new Error(result.data || 'Failed to delete project');
                }
            } catch (error) {
                console.error('Delete error:', error);
                this.showToast('Error', 'Failed to delete project. Please try again.', 'error');
            }
        },

        createProject() {
            console.log('Creating new project...'); // Debug log
            
            if (!this.newProjectName || !this.newProjectDescription) {
                this.showToast('Error', 'Please provide both a name and description for your project.', 'error');
                return;
            }
        
            const newProject = {
                id: Date.now().toString(),
                name: this.newProjectName,
                shortDescription: this.newProjectDescription,
                detailedDescription: '',
                enhancedDescription: '',
                script: '',
                narration: '',
                duration: 30,
                creditsUsed: 0,
                progress: 0,
                lastModified: new Date(),
                createdAt: new Date(),
                archived: false,
                promoPack: {
                    pressRelease: '',
                    socialPosts: '',
                    emailSequence: '',
                    miniEbook: ''
                },
                assets: [
                    { type: 'script', content: '', status: 'not started' },
                    { type: 'slides', content: '', status: 'not started' },
                    { type: 'narration', content: '', status: 'not started' },
                    { type: 'emailSequence', content: '', status: 'not started' },
                    { type: 'socialPosts', content: '', status: 'not started' }
                ]
            };
            
            console.log('New project created:', newProject); // Debug log
        
            this.projects.push(newProject);
            
            // Save the new project to WordPress database
            const formData = new URLSearchParams();
            formData.append('action', 'brightsideai_save_project');
            formData.append('nonce', brightsideaiConfig.nonce);
            formData.append('project', JSON.stringify(newProject));

            fetch(brightsideaiConfig.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: formData.toString()
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    console.log('Project saved successfully:', result); // Debug log
                    this.showToast('Success', 'Your new project has been created successfully.', 'success');
                } else {
                    console.error('Failed to save project:', result); // Debug log
                    this.showToast('Error', 'Failed to create project. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Save error:', error); // Debug log
                this.showToast('Error', 'Failed to create project. Please try again.', 'error');
            });
            
            // Reset form
            this.showNewProjectModal = false;
            this.newProjectName = '';
            this.newProjectDescription = '';
            
            // Optionally, switch to the new project
            this.setCurrentProject(newProject);
        },

        setCurrentProject(project) {
            console.log('Setting current project:', project.id); // Debug log
            
            // Set current project
            this.currentProject = project;
            
            // Load all saved content
            this.webinarDescription = project.detailedDescription || '';
            this.enhancedDescription = project.enhancedDescription || '';
            this.slideContent = project.script || '';
            this.narrationText = project.narration || '';
            this.webinarDuration = project.duration || 30;
            
            // Switch to project view
            this.view = 'project';
            this.scriptBuilderStep = 'editor';
            
            console.log('Project data loaded:', {
                description: this.webinarDescription,
                enhanced: this.enhancedDescription,
                slides: this.slideContent,
                narration: this.narrationText
            }); // Debug log
        },

        updateProject(project) {
            const index = this.projects.findIndex(p => p.id === project.id);
            if (index !== -1) {
                this.projects[index] = project;
                this.currentProject = project;
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
        
                // Save the updated project to WordPress database
                const formData = new URLSearchParams();
                formData.append('action', 'brightsideai_save_project');
                formData.append('nonce', brightsideaiConfig.nonce);
                formData.append('project', JSON.stringify(projectToUpdate));

                fetch(brightsideaiConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: formData.toString()
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        console.log('Project updated successfully:', result); // Debug log
                        this.showToast('Project Updated', 'Your project has been updated successfully.');
                    } else {
                        console.error('Failed to update project:', result); // Debug log
                        this.showToast('Error', 'Failed to update project. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Update error:', error); // Debug log
                    this.showToast('Error', 'Failed to update project. Please try again.', 'error');
                });
                
                // Close modal using Flowbite
                const modal = document.getElementById('edit-project-modal');
                if (modal) {
                    const modalInstance = new Modal(modal);
                    modalInstance.hide();
                }
                
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
            console.log('Generating script...'); // Debug log
            if (!this.enhancedDescription) {
                console.log('No enhanced description available'); // Debug log
                this.showToast('Error', 'Please enhance your description with AI first before generating the script.', 'error');
                return;
            }
            
            if (this.isGenerating) {
                console.log('Already generating script'); // Debug log
                return;
            }
        
            this.isGenerating = true;
            this.error = null;

            try {
                console.log('Making API requests...'); // Debug log

                // Generate slides
                const slidesData = new URLSearchParams();
                slidesData.append('action', 'brightsideai_generate_text');
                slidesData.append('prompt', this.enhancedDescription);
                slidesData.append('type', 'slides');
                slidesData.append('nonce', brightsideaiConfig.nonce);

                // Generate narration
                const narrationData = new URLSearchParams();
                narrationData.append('action', 'brightsideai_generate_text');
                narrationData.append('prompt', this.enhancedDescription);
                narrationData.append('type', 'narration');
                narrationData.append('nonce', brightsideaiConfig.nonce);

                console.log('Request data:', {
                    url: brightsideaiConfig.ajaxUrl,
                    nonce: brightsideaiConfig.nonce,
                    prompt: this.enhancedDescription
                }); // Debug log

                // Make both requests in parallel
                const [slidesResponse, narrationResponse] = await Promise.all([
                    fetch(brightsideaiConfig.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: slidesData.toString()
                    }),
                    fetch(brightsideaiConfig.ajaxUrl, {
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

                console.log('API responses:', { slides: slidesResult, narration: narrationResult }); // Debug log

                if (slidesResult.success && narrationResult.success) {
                    this.slideContent = slidesResult.data;
                    this.narrationText = narrationResult.data;
                    
                    if (this.currentProject) {
                        this.currentProject.script = slidesResult.data;
                        this.currentProject.narration = narrationResult.data;
                        this.currentProject.progress = Math.min(this.currentProject.progress + 20, 100);
                        this.updateProject(this.currentProject);
                    }
                    
                    this.showToast('Success', 'Your webinar script has been generated successfully!', 'success');
                } else {
                    const error = slidesResult.data || narrationResult.data || 'Error generating script';
                    console.error('Generation error:', error);
                    this.error = error;
                    this.showToast('Error', this.error, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.error = 'Failed to generate script. Please try again.';
                this.showToast('Error', this.error, 'error');
            } finally {
                this.isGenerating = false;
            }
        },
        async saveChanges() {
            console.log('Saving changes...'); // Debug log
            this.isSaving = true;
            
            try {
                if (this.currentProject) {
                    console.log('Current project found:', this.currentProject.id); // Debug log
                    
                    // Update project with all current content
                    const updatedProject = {
                        ...this.currentProject,
                        detailedDescription: this.webinarDescription,
                        enhancedDescription: this.enhancedDescription,
                        script: this.slideContent,
                        narration: this.narrationText,
                        duration: this.webinarDuration,
                        lastModified: new Date(),
                        assets: this.currentProject.assets.map(asset => {
                            switch(asset.type) {
                                case 'script':
                                    return { ...asset, content: this.slideContent, status: this.slideContent ? 'completed' : 'not started' };
                                case 'slides':
                                    return { ...asset, content: this.slideContent, status: this.slideContent ? 'completed' : 'not started' };
                                default:
                                    return asset;
                            }
                        }),
                        progress: this.calculateProgress()
                    };
                    
                    console.log('Saving updated project:', updatedProject); // Debug log

                    const formData = new URLSearchParams();
                    formData.append('action', 'brightsideai_save_project');
                    formData.append('nonce', brightsideaiConfig.nonce);
                    formData.append('project', JSON.stringify(updatedProject));

                    const response = await fetch(brightsideaiConfig.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: formData.toString()
                    });

                    const result = await response.json();
                    console.log('Save response:', result); // Debug log

                    if (result.success) {
                        // Refresh the projects list
                        await this.loadProjects();
                        
                        // Update current project with the latest data
                        const savedProject = this.projects.find(p => p.id === this.currentProject.id);
                        if (savedProject) {
                            this.currentProject = savedProject;
                        }
                        
                        this.showToast('Success', 'Your changes have been saved successfully', 'success');
                    } else {
                        throw new Error(result.data || 'Failed to save project');
                    }
                } else {
                    console.log('No current project found'); // Debug log
                    this.showToast('Error', 'No project selected. Please create or select a project first.', 'error');
                }
            } catch (error) {
                console.error('Save error:', error);
                this.showToast('Error', 'Failed to save changes. Please try again.', 'error');
            } finally {
                this.isSaving = false;
            }
        },

        // Add helper method to calculate progress
        calculateProgress() {
            let completedSteps = 0;
            let totalSteps = 3; // Description, Script, Narration
            
            if (this.webinarDescription) completedSteps++;
            if (this.slideContent) completedSteps++;
            if (this.narrationText) completedSteps++;
            
            return Math.round((completedSteps / totalSteps) * 100);
        },

        // Marketing Suite Methods
        async generateArticle() {
            console.log('Generating article...'); // Debug log
            
            if (this.isGenerating) {
                console.log('Already generating article'); // Debug log
                return;
            }

            if (!this.currentProject) {
                this.showToast('Error', 'Please select a project first.', 'error');
                return;
            }

            if (!this.articleContext) {
                this.showToast('Error', 'Please provide some context for the article.', 'error');
                return;
            }
        
            this.isGenerating = true;
            this.error = null;

            try {
                console.log('Making API request...'); // Debug log

                const formData = new URLSearchParams();
                formData.append('action', 'brightsideai_generate_article');
                formData.append('nonce', brightsideaiConfig.nonce);
                formData.append('webinar_description', this.currentProject.enhancedDescription || this.currentProject.detailedDescription || '');
                formData.append('slide_content', this.currentProject.script || '');
                formData.append('narration_text', this.currentProject.narration || '');
                formData.append('article_context', this.articleContext || '');

                const response = await fetch(brightsideaiConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: formData.toString()
                });

                const result = await response.json();
                console.log('API response:', result); // Debug log

                if (result.success) {
                    this.generatedArticle = result.data.article;
                    
                    // Update the project's assets
                    if (this.currentProject) {
                        this.currentProject.assets = this.currentProject.assets.map(asset => 
                            asset.type === 'article' ? { ...asset, content: result.data.article, status: 'completed' } : asset
                        );
                        await this.updateProject(this.currentProject);
                    }
                    
                    this.showToast('Success', 'Article generated successfully!', 'success');
                } else {
                    throw new Error(result.data || 'Failed to generate article');
                }
            } catch (error) {
                console.error('Generation error:', error);
                this.showToast('Error', error.message || 'Failed to generate article. Please try again.', 'error');
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
                
                // Save the updated credits to WordPress database
                const formData = new URLSearchParams();
                formData.append('action', 'brightsideai_update_credits');
                formData.append('nonce', brightsideaiConfig.nonce);
                formData.append('credits', this.credits);

                fetch(brightsideaiConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: formData.toString()
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        console.log('Credits updated successfully:', result); // Debug log
                    } else {
                        console.error('Failed to update credits:', result); // Debug log
                    }
                })
                .catch(error => {
                    console.error('Update error:', error); // Debug log
                });
                
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