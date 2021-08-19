
<p align="center">
  

  <h3 align="center">Public Tender Database</h3>

  <p align="center">
    A Laravel API based Tender Contract storage system!
    <br />
    
  </p>
</p>



<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>      
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Api Usage</a></li>       
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://example.com)

This is an api based system to upload tender contracts from excel documents and search through the contracts

Usage summary:
* Clone repository. rename .env.example to .env . add postgres database details. composer install. php artisan migrate. php artisan serve
* You should be able to access API documentation and endpoint at http://localhost:8000/api/documentation
* Api steps are: 1 register as user. 2 get token using registered credentials. 3 authorize demo api server with received bearer token. 

You are now ready to explore the api!

A list of detailed steps below.




<!-- GETTING STARTED -->
## Getting Started

This is a short brief of how you can get started using this api.

### Prerequisites

This system requires postgres and php >= 7.3.
* postgres
  ```sh
  sudo apt update
  sudo apt install postgresql postgresql-contrib
  ```

### Installation


1. Clone the repo
   ```sh
   git clone https://github.com/glorifiedking/public-tender-manager.git
   ```
2. Set environmental variables
   ```sh
   cd public-tender-manager
   mv .env.example .env
   nano .env
   ```
3. Install dependencies and migrate
   ```sh
   composer install
   php artisan migrate
   php arisan key:gen
   ```  
4. Start dev server
   ```sh
   php artisan serve
   ```      
5. Start laravel queue `config.js`
   ```sh
   php artisan queue:work
   ```
6. Access Api documentation (OPEN API) link
   ```
   The default link is at https://localhost:8000/api/documentation
   
   ```   



<!-- USAGE EXAMPLES -->
## Api Usage

The default base APIURL is located in `config\l5-swagger.php`. you can modify the environmental variable `L5_SWAGGER_CONST_HOST` to serve on desired host

1. create a user account on endpoint `/api/user/register`
2. create token to access routes on endpoint `/api/user/getToken`
3. Authorize demo api server using the Bearer token returned from step 2
4. You are now authenticated to use all api endpoints with Authorization token

_For all api endpoints and testing, please refer to the [Documentation](http://localhost:8000/api/documentation)_






<!-- LICENSE -->
## License

Distributed under the MIT License. See `LICENSE` for more information.



<!-- CONTACT -->
## Contact

Raymond - [@glorifiedKing]

Project Link: [https://github.com/glorifiedking/public-tender-manager](https://github.com/glorifiedking/public-tender-manager)








[product-screenshot]: public/images/screenshot.png
