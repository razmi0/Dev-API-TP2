<h1>Welcome <?= isset($user) ? $user->getUsername() : ''; ?></h1>
<section>
    <h2>What can you expect from this application ?</h2>
    <p>
        This application is a simple API that allows you to manage users. You can sign up, login, view your profile, grind the API with your api key and logout.
    </p>
    <p>
        The MVC application is built with Slim 4, PHP 8, MySQL, uses Defuse Crypto for encryption and PHP-DI for dependency injection.
    </p>
</section>
<section>
    <h2>What's next?</h2>
    <p>
        Please <a href="/login">login</a> to continue or <a href="/signup">signup</a> if you don't have an account yet.
    </p>
</section>
<section>
    <h2>The API</h2>
    <p>
        The API is protected by an API key. You can find your API key in your profile once you're logged in. Once you have your API key, you can use it to access the API endpoints by adding a header to your request :
        <code>
            X-API-KEY: YOUR_API_KEY
        </code>
    </p>
    <p>Here is a list of the endpoints : </p>
    <ul>
        <li>
            <strong>GET /api/v1.0/produit/list</strong>
            <p>This endpoints retrieve all the products in database</p>
            <p>You can add query parameters or body JSON content : </p>
            <ul>
                <li>
                    <strong>limit</strong> : The number of products to retrieve
                </li>
                <li>
                    <strong>offset</strong> : The number of products to skip
                </li>
                <li>
                    <strong>order</strong> : The order of the products ( ASC or DESC )
                </li>
            </ul>
        </li>
        <li>
            <strong>GET /api/v1.0/produit/listone/{id}</strong>
            <p>This endpoints retrieve one product in database</p>
        </li>
        <li>
            <strong>POST /api/v1.0/produit/new</strong>
            <p>Create a product in database</p>
        </li>
        <li>
            <strong>PUT /api/v1.0/produit/update</strong>
            <p>Update a product in database</p>
        </li>
        <li>
            <strong>DELETE /api/v1.0/produit/delete</strong>
            <p>Delete a product in database</p>
        </li>
    </ul>
</section>