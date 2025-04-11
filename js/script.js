// Smart Timetable Generator JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Dark mode toggle - Enhanced implementation
    const themeToggleBtn = document.getElementById('theme-toggle');
    const darkModeText = document.getElementById('dark-mode-text');
    const lightModeText = document.getElementById('light-mode-text');
    const darkModeIcon = document.getElementById('dark-mode-icon');
    const lightModeIcon = document.getElementById('light-mode-icon');
    
    // Initialize the toggle button state based on current theme
    function updateToggleState() {
        if (document.documentElement.classList.contains('dark')) {
            darkModeText.classList.add('hidden');
            lightModeText.classList.remove('hidden');
            darkModeIcon.classList.add('hidden');
            lightModeIcon.classList.remove('hidden');
        } else {
            darkModeText.classList.remove('hidden');
            lightModeText.classList.add('hidden');
            darkModeIcon.classList.remove('hidden');
            lightModeIcon.classList.add('hidden');
        }
    }
    
    // Call this once to set the initial state
    updateToggleState();
    
    themeToggleBtn.addEventListener('click', function() {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }
        updateToggleState();
    });
    
    // Add Course functionality
    const addCourseBtn = document.getElementById('add-course');
    const courseContainer = document.getElementById('course-container');
    
    addCourseBtn.addEventListener('click', function() {
        const courseEntry = document.createElement('div');
        courseEntry.className = 'course-entry flex items-center mb-2';
        
        courseEntry.innerHTML = `
            <input type="text" name="courses[]" placeholder="Course Name" class="w-1/3 px-4 py-2 border rounded-md mr-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <input type="number" name="credits[]" placeholder="Credits" min="1" max="5" class="w-1/4 px-4 py-2 border rounded-md mr-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <select name="frequency[]" class="w-1/4 px-4 py-2 border rounded-md dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="1">1x/week</option>
                <option value="2" selected>2x/week</option>
                <option value="3">3x/week</option>
                <option value="4">4x/week</option>
                <option value="5">5x/week</option>
            </select>
            <button type="button" class="remove-course ml-2 text-red-500">×</button>
        `;
        
        courseContainer.appendChild(courseEntry);
        
        // Add event listener to the new remove button
        const removeBtn = courseEntry.querySelector('.remove-course');
        removeBtn.addEventListener('click', function() {
            courseContainer.removeChild(courseEntry);
        });
    });
    
    // Add initial remove course functionality
    document.querySelectorAll('.remove-course').forEach(btn => {
        btn.addEventListener('click', function() {
            if (courseContainer.children.length > 1) {
                this.parentElement.remove();
            } else {
                alert('You need at least one course!');
            }
        });
    });
    
    // Add Conflict functionality
    const addConflictBtn = document.getElementById('add-conflict');
    const conflictContainer = document.getElementById('conflict-container');
    
    addConflictBtn.addEventListener('click', function() {
        const conflictEntry = document.createElement('div');
        conflictEntry.className = 'conflict-entry flex items-center mb-2';
        
        conflictEntry.innerHTML = `
            <select name="conflict_day[]" class="w-1/4 px-4 py-2 border rounded-md mr-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
            </select>
            <input type="time" name="conflict_start[]" class="w-1/4 px-4 py-2 border rounded-md mr-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <input type="time" name="conflict_end[]" class="w-1/4 px-4 py-2 border rounded-md mr-2 dark:bg-gray-700 dark:text-white dark:border-gray-600">
            <button type="button" class="remove-conflict text-red-500">×</button>
        `;
        
        conflictContainer.appendChild(conflictEntry);
        
        // Add event listener to the new remove button
        const removeBtn = conflictEntry.querySelector('.remove-conflict');
        removeBtn.addEventListener('click', function() {
            conflictContainer.removeChild(conflictEntry);
        });
    });
    
    // Add initial remove conflict functionality
    document.querySelectorAll('.remove-conflict').forEach(btn => {
        btn.addEventListener('click', function() {
            this.parentElement.remove();
        });
    });
    
    // Print functionality
    const printBtn = document.getElementById('print-btn');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            window.print();
        });
    }
    
    // Save functionality - using localStorage
    const saveBtn = document.getElementById('save-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function() {
            const formData = new FormData(document.getElementById('timetable-form'));
            const formObject = {};
            
            formData.forEach((value, key) => {
                // Handle array inputs like courses[]
                if (key.endsWith('[]')) {
                    const cleanKey = key.replace('[]', '');
                    if (!formObject[cleanKey]) formObject[cleanKey] = [];
                    formObject[cleanKey].push(value);
                } else {
                    formObject[key] = value;
                }
            });
            
            // Save the timetable HTML content too
            const timetableResult = document.getElementById('timetable-result');
            if (timetableResult) {
                formObject.timetableHTML = timetableResult.innerHTML;
            }
            
            localStorage.setItem('savedTimetable', JSON.stringify(formObject));
            alert('Timetable saved successfully!');
        });
    }
    
    // Load functionality
    const loadBtn = document.getElementById('load-btn');
    if (loadBtn) {
        loadBtn.addEventListener('click', function() {
            const savedData = localStorage.getItem('savedTimetable');
            if (!savedData) {
                alert('No saved timetable found!');
                return;
            }
            
            const formObject = JSON.parse(savedData);
            
            // Restore form values
            const form = document.getElementById('timetable-form');
            
            // Handle special case for arrays (courses, credits, etc)
            for (const key in formObject) {
                if (Array.isArray(formObject[key])) {
                    // Skip the timetable HTML
                    if (key === 'timetableHTML') continue;
                    
                    // For arrays, we need to create appropriate number of entries
                    const containerName = key + '-container'; // e.g. course-container
                    const container = document.getElementById(containerName);
                    
                    // Reset container to have just one entry
                    if (container) {
                        const template = container.children[0].cloneNode(true);
                        container.innerHTML = '';
                        
                        // Create as many entries as needed
                        formObject[key].forEach((value, index) => {
                            const entry = template.cloneNode(true);
                            container.appendChild(entry);
                            
                            // Set the value
                            const input = entry.querySelector(`[name="${key}[]"]`);
                            if (input) input.value = value;
                            
                            // Add remove event listener
                            const removeBtn = entry.querySelector(`.remove-${key.replace('s', '')}`);
                            if (removeBtn) {
                                removeBtn.addEventListener('click', function() {
                                    entry.remove();
                                });
                            }
                        });
                    }
                } else if (key === 'timetableHTML') {
                    // Restore the timetable display
                    const timetableResult = document.getElementById('timetable-result');
                    if (timetableResult) {
                        timetableResult.innerHTML = formObject[key];
                    }
                } else {
                    // Regular form fields
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = formObject[key] === 'on';
                        } else {
                            input.value = formObject[key];
                        }
                    }
                }
            }
            
            alert('Timetable loaded successfully!');
        });
    }
    
    // PDF Export functionality
    const pdfBtn = document.getElementById('pdf-btn');
    if (pdfBtn) {
        pdfBtn.addEventListener('click', function() {
            // Submit the form to the PDF generation endpoint
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'includes/generate_pdf.php';
            form.target = '_blank';
            
            // Copy the timetable HTML as a hidden input
            const timetableResult = document.getElementById('timetable-result');
            if (timetableResult) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'timetable_html';
                input.value = timetableResult.innerHTML;
                form.appendChild(input);
            }
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });
    }
    
    // Regenerate button
    const regenerateBtn = document.getElementById('regenerate-btn');
    if (regenerateBtn) {
        regenerateBtn.addEventListener('click', function() {
            document.getElementById('timetable-form').submit();
        });
    }
    
    // Toggle insight details for expandable cards
    window.toggleInsightDetails = function(index) {
        const detailsElement = document.getElementById(`insight-details-${index}`);
        const arrowElement = document.getElementById(`insight-arrow-${index}`);
        
        if (detailsElement) {
            const isHidden = detailsElement.classList.contains('hidden');
            
            if (isHidden) {
                detailsElement.classList.remove('hidden');
                arrowElement?.classList.add('rotate-180');
            } else {
                detailsElement.classList.add('hidden');
                arrowElement?.classList.remove('rotate-180');
            }
        }
    };
    
    // Recommendations carousel functionality
    const carouselElement = document.getElementById('recommendations-carousel');
    if (carouselElement) {
        const slides = document.getElementById('carousel-slides');
        const prevBtn = document.getElementById('prev-rec');
        const nextBtn = document.getElementById('next-rec');
        const dots = document.querySelectorAll('.carousel-dot');
        
        let currentSlide = 0;
        const slideCount = dots.length;
        
        // Initialize the active dot
        if (dots.length > 0) {
            dots[0].classList.add('bg-amber-500', 'dark:bg-amber-400');
        }
        
        const goToSlide = function(index) {
            if (index < 0) index = slideCount - 1;
            if (index >= slideCount) index = 0;
            
            currentSlide = index;
            slides.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            // Update active dot
            dots.forEach((dot, i) => {
                if (i === currentSlide) {
                    dot.classList.add('bg-amber-500', 'dark:bg-amber-400');
                    dot.classList.remove('bg-gray-300', 'dark:bg-gray-600');
                } else {
                    dot.classList.remove('bg-amber-500', 'dark:bg-amber-400');
                    dot.classList.add('bg-gray-300', 'dark:bg-gray-600');
                }
            });
        };
        
        // Add click event to dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => goToSlide(index));
        });
        
        // Add event listeners to prev/next buttons
        if (prevBtn) {
            prevBtn.addEventListener('click', () => goToSlide(currentSlide - 1));
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => goToSlide(currentSlide + 1));
        }
    }
});