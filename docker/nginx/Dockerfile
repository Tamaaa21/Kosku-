# Stage 1: Build Vue 3 Frontend SPA
FROM node:20-alpine AS frontend-builder
WORKDIR /app

# Cache package dependencies
COPY frontend/package*.json ./
RUN npm ci || npm install

# Copy frontend source code
COPY frontend/ ./

# Build frontend with relative API path
ARG VITE_API_URL=/api/v1
ENV VITE_API_URL=$VITE_API_URL
RUN npm run build

# Stage 2: Nginx Web Server & Reverse Proxy
FROM nginx:alpine
WORKDIR /var/www/html

# Copy custom Nginx configuration
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy compiled frontend SPA
COPY --from=frontend-builder /app/dist /var/www/frontend

# Copy Laravel public directory (entry file & static fallback)
COPY public /var/www/html/public

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
