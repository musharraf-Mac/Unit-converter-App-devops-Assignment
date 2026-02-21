# Unit Converter Application

## Group information
- **Student 1:** Abdur Rahman Musharraf - ITBIN-2313-0067 - Role: Devop engineer
- **Student 2:** Kannagi Theebapriya - ITBIN-2313-0116 - Role: Full stack developer

## Project description
A web-based Unit Converter Application that enables users to seamlessly convert values between different units of measurement. The application supports multiple conversion categories, including length, weight, and temperature. Additionally, users can view their past conversion history for quick reference and convenience.

## Live deployment
**Live URL:** https://unit-converter-app-devops-assignmen.vercel.app/

## Technologies used
- HTML5, CSS, JavaScript, PHP, MySQL
- GitHub Actions
- Vercel

## Features
- **Length Conversion:** Convert between meters, kilometers,centimeters
- **Weight Conversion:** Convert between kilograms, grams, pound
- **Temperature Conversion:** Convert between Celsius, Fahrenheit, and Kelvin
- **User-Friendly Interface:** Clean and responsive design for easy navigation
- **History:** Users can view their past conversions

## Branch Strategy
- `main` - Production branch
- `develop` - Integration branch
- `feature/*` - Feature development branches

## Individual Contributions

## Abdur Rahman Musharraf
- Repository setup and configuration
- Github Actions CI/CD pipeline implementation
- Deployment setup and management
- Created database for it
- Created PHP file for save all the entries to the database

## Kannagi Theebapriya
- Created UI for the App
- Created all the conversion features
- Committed all the application
- Created history feature
- Created pull request for push all the feature code implementation(Develop) branch

## Setup instruction

### Prerequisites
- Node.js (version 18 or higher)
- Git

### Installation
```bash
# Clone the repository
git clone https://github.com/musharraf-Mac/Unit-converter-App-devops-Assignment

# Navigate to project directory
cd Unit-converter-App-devops-Assignment

# Deployment Process
This project uses GitHub Actions for Continuous Integration and Continuous Deployment - **Trigger:** Automatically runs on every `push` or `pull request` to `main` and `develop` branches
- **Steps:** Code checkout → Build Docker image → Run tests → Deploy to Vercel
- **Deployment:** Successful builds on `main` are automatically deployed to Vercel

#Build the Docker Image
docker build -t unit-converter-app

#Run with docker compose (Recommanded)
docker-compose up --build

# To see the running app 
```
this at your browser address : **http://localhost:8080**
```bash
#To stop the app
docker-compose down

# Challanges Faced
We faced some critical errors while deployement. It is took many of my time to fix that. 

# Build status
```  
## Docker Commands Reference

| Command | Description |
|--------|-------------|
| `docker compose up --build` | Build and start the container |
| `docker compose up -d` | Start in detached (background) mode |
| `docker compose down` | Stop and remove containers |
| `docker compose logs -f` | View live container logs |
| `docker ps` | List running containers |
| `docker build -t unit-converter-app .` | Build image manually |
| `docker run -p 8080:80 unit-converter-app` | Run image manually |

## Port Configuration

- The application runs on **port 80** inside the container.
- It is mapped to **port 8080** on your local machine (configurable in `docker-compose.yml`).




