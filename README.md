![alt text](Laravel_Vue_E-commerce_App.png)

<details>
  <summary>An e-commerce platform build with the help of Laravel and Vue.</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#honorable-mention">Honorable mention</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

There weren't really that many great examples of e-commerce applications being build using the combination of the two (Laravel and Vue) and thus I decided to give it a try myself. I could only find one great tutorial on the web in particular, which covered the fundamentals. I decided to expand that application after reading the tutorial because I saw a lot of potential.

I don't necessarily think it's bad to work with someone else's code, as long as you can understand it, learn to cope with the frameworks and develop new features on your own. And so I took on this challenge, soon after I realised that it was rather fun working on my own hobby project.

Never before did I use Vue JS, but a friend of mine inspired me to make use of it. I think that progressive front-end frameworks are really awesome, it gives a whole new meaning to front-end development. Over the past few months I have learned a lot from Vue JS and I am excited to keep on working on this project in my free-time!

<p align="right">(<a href="#top">back to top</a>)</p>



### Built With

Here is a list of the most important frameworks/libraries that I used to build this application.

* [Vue.js](https://vuejs.org/)
* [Vue-Loader](https://vue-loader.vuejs.org/)
* [PrimeVue](https://www.primefaces.org/primevue/showcase-v2/)
* [Vuex](https://vuex.vuejs.org/)
* [Vue-Router](https://router.vuejs.org/)
* [LocalTunnel](https://www.npmjs.com/package/localtunnel)
* [Laravel](https://laravel.com)
* [Bootstrap](https://getbootstrap.com)
* [JQuery](https://jquery.com)

<p align="right">(<a href="#top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

Let's try and run this "bad boy" on our own local computer shall we?

### Prerequisites

To clone and run this application, it is important to have [Git](/https://git-scm.com/ "Git"), [Node.JS](https://nodejs.org/en/download/ "Node.JS") and [Composer](https://getcomposer.org/download/ "Composer") pre-installed.
### Installation

_Below are some instructions on how to get this application up and running on a local server._

1. Create a new [Mollie account](https://www.mollie.com/ "Mollie account") 

2. Head over to config/mollie.php and enter your test API key
1. Clone the repository

   ```sh
   git clone https://github.com/SanderP98/vue-laravel-ecommerce.git
   ```
2. Start your SQL server and create a new database
3. Rename the .env.example file to .env and change the database name properly

4. Generate the application encryption key
   ```sh
	php artisan key:generate
   ```
5. Install NPM packages

   ```sh
   npm install
   npm i vue-loader
   ```
6. Install Composer packages
   ```sh
   composer install
   ```
7. Migrate the database

   ```sh
   php artisan migrate
   ```
8. Install Laravel Passport

   ```sh
   php artisan passport:install
   ```
9. Change the credentials of the "UsersTableSeeder" and execute the seeder

   ```sh
   php artisan db:seed
   ```
10. Start the webserver, the hot reloader and the SSH tunnel for our webhook

   ```sh
   php artisan serve
   lt -p 8000
   npm run hot
   ```


<p align="right">(<a href="#top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

Once everything is set up, you can visit your local server and click on the "My Account" dropdown menu in the topright corner. Now just log in with your given credentials and you should get redirected to the administrator dashboard. 

From here you can click on the "setup" button in the sidebar, this will open the quick setup wizard for your web store. 

_( Keep in mind that this is installation wizard is not completed yet. For example there is an input field for your Mollie API key, but it does nothing yet. )_

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- CONTACT -->
## Contact

Sander Plomp - [Twitter](https://twitter.com/SanderPlomp_) - [LinkedIn](https://www.linkedin.com/in/sander-p-4524ab129/) - sander.plomp@live.nl

Project Link: [https://github.com/SanderP98/vue-laravel-ecommerce](https://github.com/SanderP98/vue-laravel-ecommerce)

<p align="right">(<a href="#top">back to top</a>)</p>

<!-- Honorable mention -->
## Honorable mention

Neo Ighodaro - [Build an e-commerce application using Laravel and Vue – Part 1: Application Setup](https://blog.pusher.com/ecommerce-laravel-vue-part-1/)