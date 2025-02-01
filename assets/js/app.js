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

        async createProject() {
            if (!this.newProjectName || !this.newProjectDescription) {
                this.showToast('Error', 'Please provide both a name and description for your project.', 'error');
                return;
            }

            try {
                const formData = new URLSearchParams();
                formData.append('action', 'brightsideai_save_project');
                formData.append('nonce', brightsideaiConfig.nonce);
                
                const newProject = {
                    title: this.newProjectName,
                    description: this.newProjectDescription,
                    enhancedDescription: '',
                    script: '',
                    narration: '',
                    duration: '15m'
                };

                formData.append('project', JSON.stringify(newProject));

                const response = await fetch(brightsideaiConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: formData.toString()
                });

                const result = await response.json();
                if (result.success) {
                    // Refresh projects list
                    await this.loadProjects();
                    
                    // Clear the form
                    this.newProjectName = '';
                    this.newProjectDescription = '';
                    this.showNewProjectModal = false;
                    
                    // Set as current project
                    this.setCurrentProject(result.data);
                    
                    this.showToast('Success', 'Project created successfully!', 'success');
                } else {
                    throw new Error(result.data || 'Failed to create project');
                }
            } catch (error) {
                console.error('Project creation error:', error);
                this.showToast('Error', error.message || 'Failed to create project', 'error');
            }
        },

        async loadProjects() {
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
                if (result.success) {
                    this.projects = result.data;
                    console.log('Projects loaded:', this.projects); // Debug log
                } else {
                    throw new Error(result.data || 'Failed to load projects');
                }
            } catch (error) {
                console.error('Project loading error:', error);
                this.showToast('Error', error.message || 'Failed to load projects', 'error');
            }
        },

        async setCurrentProject(project) {
            console.log('Setting current project:', project); // Debug log
            
            if (!project) {
                console.log('No project provided to setCurrentProject'); // Debug log
                this.currentProject = null;
                this.clearProjectState();
                return;
            }

            try {
                // Load fresh project data
                const formData = new URLSearchParams();
                formData.append('action', 'brightsideai_get_project');
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
                console.log('Project load result:', result); // Debug log

                if (result.success) {
                    // Clear previous state
                    this.clearProjectState();
                    
                    // Set new project and update UI state
                    this.currentProject = result.data;
                    this.webinarDescription = this.currentProject.description || '';
                    this.enhancedDescription = this.currentProject.enhancedDescription || '';
                    this.slideContent = this.currentProject.script || '';
                    this.narrationText = this.currentProject.narration || '';
                    this.webinarDuration = this.currentProject.duration || '15m';
                    
                    console.log('Project state updated:', {
                        currentProject: this.currentProject,
                        webinarDescription: this.webinarDescription,
                        enhancedDescription: this.enhancedDescription
                    }); // Debug log
                } else {
                    throw new Error(result.data || 'Failed to load project data');
                }
            } catch (error) {
                console.error('Error in setCurrentProject:', error);
                this.showToast('Error', error.message || 'Failed to load project data', 'error');
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

        async editProject(project) {
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

        async saveProjectChanges() {
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

        async enhanceDescription() {
            console.log('Enhance called. Current project:', this.currentProject); // Debug log
            
            if (!this.currentProject || !this.currentProject.id) {
                console.error('No project selected or invalid project:', this.currentProject);
                this.showToast('Error', 'Please select or create a project first.', 'error');
                return;
            }

            if (!this.webinarDescription) {
                this.showToast('Error', 'Please enter a description first.', 'error');
                return;
            }

            this.isEnhancing = true;
            try {
                const formData = new URLSearchParams();
                formData.append('action', 'brightsideai_generate_text');
                formData.append('nonce', brightsideaiConfig.nonce);
                formData.append('prompt', this.webinarDescription);
                formData.append('type', 'enhance');

                console.log('Sending enhance request with data:', {
                    prompt: this.webinarDescription,
                    projectId: this.currentProject.id
                }); // Debug log

                const response = await fetch(brightsideaiConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: formData.toString()
                });

                const result = await response.json();
                console.log('Enhancement API response:', result); // Debug log

                if (result.success) {
                    // Update both the display and project data
                    this.enhancedDescription = result.data;
                    this.currentProject.enhancedDescription = result.data;
                    
                    // Save the changes to the database
                    const updatedProject = {
                        ...this.currentProject,
                        enhancedDescription: result.data
                    };

                    console.log('Saving updated project:', updatedProject); // Debug log

                    const saveFormData = new URLSearchParams();
                    saveFormData.append('action', 'brightsideai_save_project');
                    saveFormData.append('nonce', brightsideaiConfig.nonce);
                    saveFormData.append('project', JSON.stringify(updatedProject));

                    const saveResponse = await fetch(brightsideaiConfig.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: saveFormData.toString()
                    });

                    const saveResult = await saveResponse.json();
                    console.log('Save result:', saveResult); // Debug log

                    if (saveResult.success) {
                        this.showToast('Success', 'Description enhanced and saved successfully!', 'success');
                    } else {
                        throw new Error('Failed to save enhanced description');
                    }
                } else {
                    throw new Error(result.data || 'Failed to enhance description');
                }
            } catch (error) {
                console.error('Enhancement error:', error);
                this.showToast('Error', error.message || 'Failed to enhance description. Please try again.', 'error');
            } finally {
                this.isEnhancing = false;
            }
        },

        async generateScript() {
            if (!this.currentProject) {
                this.showToast('Error', 'Please select or create a project first.', 'error');
                return;
            }

            if (!this.enhancedDescription) {
                this.showToast('Error', 'Please enhance the description first.', 'error');
                return;
            }

            this.isGenerating = true;
            try {
                // Generate slides
                const slideFormData = new URLSearchParams();
                slideFormData.append('action', 'brightsideai_generate_text');
                slideFormData.append('nonce', brightsideaiConfig.nonce);
                slideFormData.append('prompt', this.enhancedDescription);
                slideFormData.append('type', 'slides');

                const narrationFormData = new URLSearchParams();
                narrationFormData.append('action', 'brightsideai_generate_text');
                narrationFormData.append('nonce', brightsideaiConfig.nonce);
                narrationFormData.append('prompt', this.enhancedDescription);
                narrationFormData.append('type', 'narration');

                const [slideResponse, narrationResponse] = await Promise.all([
                    fetch(brightsideaiConfig.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: slideFormData.toString()
                    }),
                    fetch(brightsideaiConfig.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: narrationFormData.toString()
                    })
                ]);

                const [slideResult, narrationResult] = await Promise.all([
                    slideResponse.json(),
                    narrationResponse.json()
                ]);

                if (slideResult.success && narrationResult.success) {
                    // Update both the display and project data
                    this.slideContent = slideResult.data;
                    this.narrationText = narrationResult.data;
                    this.currentProject.script = slideResult.data;
                    this.currentProject.narration = narrationResult.data;

                    // Save the changes to the database
                    const updatedProject = {
                        ...this.currentProject,
                        script: slideResult.data,
                        narration: narrationResult.data
                    };

                    const saveFormData = new URLSearchParams();
                    saveFormData.append('action', 'brightsideai_save_project');
                    saveFormData.append('nonce', brightsideaiConfig.nonce);
                    saveFormData.append('project', JSON.stringify(updatedProject));

                    const saveResponse = await fetch(brightsideaiConfig.ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        },
                        body: saveFormData.toString()
                    });

                    const saveResult = await saveResponse.json();
                    if (saveResult.success) {
                        this.showToast('Success', 'Script generated and saved successfully!', 'success');
                    } else {
                        throw new Error('Failed to save generated script');
                    }
                } else {
                    throw new Error('Failed to generate script');
                }
            } catch (error) {
                console.error('Generation error:', error);
                this.showToast('Error', error.message || 'Failed to generate script. Please try again.', 'error');
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