# syntax=docker/dockerfile:1

# --- dev stage: hot-reloading vite dev server ------------------------------
# Used by compose.yml (bind-mounts source, runs `npm run dev`).
FROM node:18-alpine AS dev
WORKDIR /app/vite
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
EXPOSE 3000
CMD ["npm", "run", "dev"]

# --- build stage: produces the static production bundle -------------------
FROM node:18-alpine AS build
WORKDIR /app/vite
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- production stage: static files served by nginx, no Node at runtime ---
# Used by compose.prod.yml (target: production).
FROM nginx:1.27-alpine AS production
COPY --from=build /app/vite/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
