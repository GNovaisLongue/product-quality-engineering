# Vite + PHP + MySQL + Docker

Test project running on docker.

## Description

Project previously used as a test assignment for Scandiweb Developer position. The project is a simple CRUD application using Vite, PHP, and MySQL. It can be run both with and without Docker.
Salvaged for QA testing and learning purposes.

## Getting Started

### Dependencies

* WSL and Docker
* VSCode or any other IDE
* In case of not using docker, node.js, a local server with PHP and MySQL installed is required.

## Installing

### With Docker

* Execute `docker compose up --build` to build and start the project.
If you want to stop and remove everything associated to this project from your docker, execute `docker compose down -v --rmi 'all'`

### Without Docker

* cd into the `backend` folder then use the following command to install [composer](https://getcomposer.org) dependencies:

```bash
  composer install
```

* cd into the `vite` folder then use the following command to install node dependencies:

```bash
  npm install
```

* To run the frontend without any data or access to the database, execute the following command:

```bash
  npm run dev
```

* In order to have the MySQL running locally and connected with the backend, it will be required to run an APACHE server containing the backend files. Inside `mysql` folder, there's a dump that can be used to create and populate the table used for this project. Make sure the ports on the frontend, backend and MySQL are matching.

## Localhost

* [http://localhost:3000](http://localhost:3000) for vite project.
* [http://localhost:8080](http://localhost:8080) for mysql database.
* [http://localhost:8000](http://localhost:8000) for apache server.

## Help

You may find yourself having version issues with docker, node, npm, composer, or any other dependency. In case of any issues, please check the official documentation for each dependency.
