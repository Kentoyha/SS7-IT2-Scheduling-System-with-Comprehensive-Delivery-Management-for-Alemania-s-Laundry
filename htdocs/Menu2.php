
<nav>  
    <ul>
        <li><a href="Home1.php">Home</a></li>
        <li><a href="Orders2.php">Laundry Orders</a></li>
        <li><a href="Delivery1.php">Deliveries</a></li>
        <li><a href="Pickup.php">Pick ups</a></li>
        <li><a href="Receipt.php">Receipts</a></li>
      
        
     </ul>
        </nav>
        <style>
        /* General Reset */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        nav {
            
            background-color: #0078d7; /* Matches the black background in the image */
            padding: 0px;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* Center the menu items */
            align-items: center;
            height: 80px;
        }

        nav ul li {
            margin: 0 15px; /* Spacing between menu items */
        }

        nav ul li a {
    text-decoration: none;
    color: #fff; /* White text color */
    font-size: 22px;
    font-weight: bold;
    padding: 10px 15px; /* Add padding to make the clickable area larger */
    transition: background-color 0.3s ease, color 0.3s ease;
    border-radius: 5px; /* Optional: adds rounded corners */
}

nav ul li a:hover {
    background-color: #ffffff;
    color: #0078d7; /* Your navbar color for consistency */
    text-decoration: none;
    border-radius: 5px;
}
                </style>