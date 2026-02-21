# Use lightweight Nginx image to serve static files
FROM nginx:alpine

# Copy frontend files to Nginx's default serving directory
COPY . /usr/share/nginx/html

# Expose port 80
EXPOSE 80

