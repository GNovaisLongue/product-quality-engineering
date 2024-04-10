# Vite + PHP + MySQL + Docker

Simple test project running on docker.
I advice creating a separate folder and cloning the project inside of it.

## With Docker

Simply execute `docker compose up --build` to build and start the project.
If you want to stop and remove everything associated to this project from your docker, execute `docker compose down -v --rmi 'all'`

## Without Docker

- cd into the `backend` folder then use the following command to install [composer](https://getcomposer.org) dependencies:

```bash
  composer install
```

- cd into the `vite` folder then use the following command to install node dependencies:

```bash
  npm install
```

- To run the frontend without any data or access to the database, simply execute the following command:

```bash
  npm run dev
```

- In order to have the MySQL running locally and connected with the backend, it will be required to run an APACHE server containing the backend files. Inside `mysql` folder, there's a dump that can be used to create and populate the table used for this project. Make sure the ports on the frontend, backend and MySQL are matching.

## Localhost

- [http://localhost:3000](http://localhost:3000) for vite project.
- [http://localhost:8080](http://localhost:8080) for mysql database.
- [http://localhost:8000](http://localhost:8000) for apache server.

## Scandiweb Junior Developer test assignment

### The following project was bootstrapped with [Create React App](https://github.com/facebook/create-react-app)

Project contains dependencies both from npm and composer, requiring `npm install` and `composer install` to be executed after pull.

This project is being hosted using [000webhost](https://www.000webhost.com) and can be accessed [here](https://juniortest-guilherme-nl.000webhostapp.com/) (link verified using Google Chrome, Microsoft Edge, Firefox, Opera, and Opera GX. Tested and accessible on Google Chrome and Microsoft Edge).
