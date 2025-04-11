# PlanPilot Timetable Planning System

## Overview
PlanPilot is an academic timetable planning system that helps students organize their academic schedules efficiently. The application uses AI to generate optimized timetables based on user input and provides insights for improved learning efficiency.

## Features
- AI-powered timetable generation
- Interactive timetable interface
- PDF export capability
- Schedule insights and recommendations
- User authentication system

## Technologies Used
- PHP
- HTML/CSS/JavaScript
- dompdf for PDF generation
- Google API for authentication
- AI integration for timetable optimization

## Installation
1. Clone the repository to your web server's document root:
   ```
   git clone https://github.com/Rajathraj12/Plan-PIlot-Smart-time-table-generator.git
   ```

2. Ensure your web server (Apache, Nginx, etc.) is configured correctly.

3. Set up environment variables:
   - Create a copy of `env_example.php` as `env_loader.php` in the includes directory
   - Update with your API keys and configuration

4. Open the application in your browser:
   ```
   http://localhost/Plan-PIlot-Smart-time-table-generator/
   ```

## Usage
1. Log in to the application
2. Navigate to the timetable creation page
3. Input your course preferences and constraints
4. Generate and customize your timetable
5. Export to PDF or save for future reference

## Project Structure
- `/css` - Stylesheet files
- `/js` - JavaScript files
- `/includes` - PHP helper files and APIs
- `/dompdf-2.0.3` - PDF generation library
- `/vendor` - Composer dependencies

## License
[Add your chosen license here]

## Contributors
- [Your name]
- [Other contributors]

## Acknowledgements
- dompdf library for PDF generation
- Google API for authentication services
