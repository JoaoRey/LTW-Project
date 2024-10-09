

    DROP TABLE IF EXISTS User;
    DROP TABLE IF EXISTS Item;
    DROP TABLE IF EXISTS Payment;
    DROP TABLE IF EXISTS Cart;
    DROP TABLE IF EXISTS ProductCategory;
    DROP TABLE IF EXISTS Communication;
    DROP TABLE IF EXISTS ItemCategory;
    DROP TABLE IF EXISTS ItemBrand;
    DROP TABLE IF EXISTS ItemCondition;
    DROP TABLE IF EXISTS ItemSize;
    DROP TABLE IF EXISTS ItemModel;
    DROP TABLE IF EXISTS ItemImage;
    DROP TABLE IF EXISTS ProductImage;
    DROP TABLE IF EXISTS Review;
    DROP TABLE IF EXISTS Wishlist;




    /*******************************************************************************
    Create Tables
    ********************************************************************************/




    -- User Table
    CREATE TABLE User
    (
        UserId INTEGER NOT NULL,
        FirstName NVARCHAR(40)  NOT NULL,
        LastName NVARCHAR(20)  NOT NULL,
        Username TEXT NOT NULL UNIQUE,
        Email NVARCHAR(60) NOT NULL,
        Password NVARCHAR(40) NOT NULL,
        JoinDate DATE DEFAULT CURRENT_DATE,
        Address NVARCHAR(70),
        City NVARCHAR(40),
        District NVARCHAR(40),
        Country NVARCHAR(40),
        PostalCode NVARCHAR(10),
        Phone NVARCHAR(24),
        ImageUrl NVARCHAR(255),
        Admin BOOLEAN NOT NULL,
        
        CONSTRAINT PK_User PRIMARY KEY  (UserId)
    );

    -- Item Table
    CREATE TABLE Item
    (
    ItemId INTEGER  NOT NULL,
    SellerId INTEGER,
    Title NVARCHAR(160)  NOT NULL,
    Description NVARCHAR(200),
    Price NUMERIC(10,2)  NOT NULL,
    ListingDate DATE DEFAULT CURRENT_DATE,
    Active BOOLEAN DEFAULT 1,
    ImageId INTEGER,
    BrandId INTEGER,
    ConditionId INTEGER,
    SizeId INTEGER,
    ModelId INTEGER,
    CONSTRAINT PK_Item PRIMARY KEY  (ItemId),
    FOREIGN KEY (SellerId) REFERENCES User (UserId) 
        ON DELETE NO ACTION ON UPDATE NO ACTION,
    FOREIGN KEY (BrandId) REFERENCES ItemBrand (BrandId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION,
    FOREIGN KEY (ConditionId) REFERENCES ItemCondition (ConditionId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION,
    FOREIGN KEY (SizeId) REFERENCES ItemSize (SizeId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION,
    FOREIGN KEY (ModelId) REFERENCES ItemModel (ModelId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION,
    FOREIGN KEY (ImageId) REFERENCES ItemImage (ImageId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION
        
    );

    -- Payment Table
    CREATE TABLE Payment
    (
        PaymentId INTEGER  NOT NULL,
        BuyerId INTEGER,
        SellerId INTEGER,
        ItemId INTEGER,
        Address NVARCHAR(70),
        City NVARCHAR(40),
        District NVARCHAR(40),
        Country NVARCHAR(40),
        PostalCode NVARCHAR(10),
        PaymentDate DATE DEFAULT CURRENT_DATE,
        CONSTRAINT PK_Payment PRIMARY KEY  (PaymentId),
        FOREIGN KEY (BuyerId) REFERENCES User (UserId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION,
        FOREIGN KEY (SellerId) REFERENCES User (UserId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION,
        FOREIGN KEY (ItemId) REFERENCES Item (ItemId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION
    );

    -- Shopping Cart Table
    CREATE TABLE Cart
    (
        CartId INTEGER PRIMARY KEY AUTOINCREMENT,
        UserId INTEGER,
        ItemId INTEGER,
        Quantity INTEGER,
        FOREIGN KEY (UserId) REFERENCES User (UserId) 
            ON DELETE CASCADE ON UPDATE NO ACTION,
        FOREIGN KEY (ItemId) REFERENCES Item (ItemId) 
            ON DELETE CASCADE ON UPDATE NO ACTION
    );

        CREATE TABLE Wishlist (
        WishlistId INTEGER PRIMARY KEY AUTOINCREMENT ,
        UserId INT NOT NULL,
        ItemId INT NOT NULL,
        FOREIGN KEY (UserId) REFERENCES Users(UserId)
           ON DELETE CASCADE ON UPDATE NO ACTION,
        FOREIGN KEY (ItemId) REFERENCES Item(ItemId)
          ON DELETE CASCADE ON UPDATE NO ACTION
);

    -- Communication Table
    CREATE TABLE Communication
    (
        CommunicationId INTEGER PRIMARY KEY AUTOINCREMENT,
        SenderId INTEGER,
        ReceiverId INTEGER,
        ItemId INTEGER,
        CommunicationText Text,
        SendDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (SenderId) REFERENCES User (UserId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION,
        FOREIGN KEY (ReceiverId) REFERENCES User (UserId) 
            ON DELETE NO ACTION ON UPDATE NO ACTION,
        FOREIGN KEY (ItemId) REFERENCES Item (ItemId)
            ON DELETE NO ACTION ON UPDATE NO ACTION
    );


    -- Product-Category Table
    CREATE TABLE ProductCategory
    (  
    CategoryId INTEGER PRIMARY KEY,
    CategoryName NVARCHAR(100) NOT NULL

    
    );

    -- Item-Category Table
    CREATE TABLE ItemCategory
    (
    ItemId INTEGER,
    CategoryId INTEGER,
    FOREIGN KEY (ItemId) REFERENCES Item (ItemId) 
            ON DELETE CASCADE ON UPDATE NO ACTION,

    FOREIGN KEY (CategoryId) REFERENCES ProductCategory (CategoryId) 
            ON DELETE CASCADE ON UPDATE NO ACTION   
    );

    -- Item-Brand Table
    CREATE TABLE ItemBrand
    (
    BrandId INTEGER PRIMARY KEY,
    BrandName NVARCHAR(100) NOT NULL
    );

    -- Item-Condition Table
    CREATE TABLE ItemCondition
    (
    ConditionId INTEGER PRIMARY KEY,
    ConditionName NVARCHAR(100) NOT NULL
    );

    -- Item-Size Table
    CREATE TABLE ItemSize
    (
    SizeId INTEGER PRIMARY KEY,
    SizeName NVARCHAR(100) NOT NULL
    );

    -- Item-Model Table
    CREATE TABLE ItemModel
    (
    ModelId INTEGER PRIMARY KEY,
    ModelName NVARCHAR(100) NOT NULL
    );

    CREATE TABLE ProductImage
    (
        ImageId INTEGER PRIMARY KEY,
        ImageUrl NVARCHAR(255)
    );
    CREATE TABLE ItemImage
    (
        ItemId INTEGER,
        ImageId INTEGER,
        FOREIGN KEY (ItemId) REFERENCES Item (ItemId) 
            ON DELETE CASCADE ON UPDATE NO ACTION
        FOREIGN KEY (ImageId) REFERENCES ProductImage (ImageId)
            ON DELETE CASCADE ON UPDATE NO ACTION

        
    );

     -- Review Table
    CREATE TABLE Review (
        ReviewId INTEGER PRIMARY KEY,
        UserId INTEGER,
        ItemId INTEGER,
        Rating FLOAT,
        Comment TEXT,
        ReviewDate DATE DEFAULT CURRENT_DATE,
        FOREIGN KEY (UserId) REFERENCES User (UserId) ON DELETE NO ACTION ON UPDATE NO ACTION,
        FOREIGN KEY (ItemId) REFERENCES Item (ItemId) ON DELETE NO ACTION ON UPDATE NO ACTION
    );
    /*
    EXTRA FEATURES



    
    */


    /*******************************************************************************
    Populate Tables
    ********************************************************************************/
    -- Inserir dados de exemplo na tabela User
    INSERT INTO User (UserId, FirstName, LastName, Username, Email, Password, Address, City, District, Country, PostalCode, Phone, ImageUrl, Admin)
    VALUES
    (1, 'Antero', 'Morgado', 'Tero', 'antero@gmail.com', '$2y$10$AbKJuW4fLEPWNdR0Xjt2keg9uRVjxt3p7fzRx4rtEBZQcS5WNwg6K', '123 Main St', 'Guarda', 'Guarda', 'Portugal', '12345', '123-456-7890', '../database/uploads/user_1/Screenshot from 2024-05-19 23-37-43.png', 1),
    (2, 'João', 'Torres', 'Juca', 'juca@gmail.com', '$2y$10$qUYzibLiIgxJ0/7JyepO0.2XGKtpaEuw55kyf.yMeDC29nJuW9Vz6', '456 Oak St', 'Esposende', 'Braga', 'Portugal', '54321', '987-654-3210', '../database/uploads/user_2/Untitled.jpeg', 1),
    (3, 'Tiago', 'Pinto', 'Tiago', 'tiago@gmail.com', '$2y$10$Hw9Rhp.Rp8yQHYeQYc6K/.DytkKaI9Z.3vYjsm.kyfn1WSEEVUkVy', 'Rua Faria de Cima, nº158', 'Cucujães', 'Aveiro', 'Portugal', '372-785', '933774667', '../database/uploads/user_3/file.jpeg', 1),
    (4, 'Maria', 'Silva', 'mariasilva', 'mariasilva@example.com', 'password789', '789 Elm St', 'Ovar', 'Aveiro', 'Portugal', '4200-721', '919919911', '../database/uploads/default_user.png', 0),
    (5, 'Carlos', 'Santos', 'carlossantos', 'carlossantos@example.com', 'passwordabc', '101 Pine St', 'Ohio', 'California', 'EUA', '54321', '123-555-7890', '../database/uploads/default_user.png', 0);

    -- Inserir dados de exemplo na tabela Item
    INSERT INTO Item (ItemId, SellerId, Title, Description, Price, ListingDate, ImageId, BrandId, ConditionId, SizeId, ModelId)
    VALUES
    (1, 1, 'Samsung Galaxy S20', 'Brand new Samsung Galaxy S20 smartphone', 799.99, CURRENT_DATE, 1, 1, 1, 2, 1),
    (2, 1, 'Nike Air Max 270', 'Nike Air Max 270 shoes', 129.99, CURRENT_DATE, 2, 3, 1, 3, 2),
    (3, 2, 'Apple MacBook Pro', 'Used MacBook Pro in good condition', 1499.99, CURRENT_DATE, 3, 2, 3, 1, 3),
    (4, 2, 'Nike Air Force 1', 'Classic Nike Air Force 1 shoes', 99.99, CURRENT_DATE, 4, 3, 1, 2, 4),
    (5, 3, 'Adidas Superstar', 'Adidas Superstar sneakers', 79.99, CURRENT_DATE, 5, 4, 1, 3, 5),
    (6, 4, 'Sony PlayStation 5', 'Brand new Sony PlayStation 5 console', 499.99, CURRENT_DATE, 6, 5, 1, 1, 6),
    (7, 5, 'Rolex Submariner', 'Used Rolex Submariner watch in excellent condition', 8999.99, CURRENT_DATE, 7, 6, 2, 1, 7),
    (8, 3, 'Apple iPhone 12', 'Used iPhone 12 in good condition', 699.99, CURRENT_DATE, 8, 2, 3, 1, 8),
    (9, 2, 'Adidas Yeezy Boost 350', 'Adidas Yeezy Boost 350 shoes', 219.99, CURRENT_DATE, 9, 4, 1, 3, 9),
    (10, 1, 'Samsung QLED TV', 'Brand new Samsung QLED TV', 1299.99, CURRENT_DATE, 10, 1, 1, 1, 10),
    (11, 4, 'LG OLED TV', 'Brand new LG OLED TV', 1799.99, CURRENT_DATE, 11, 7, 1, 1, 10),
    (12, 5, 'Microsoft Surface Pro', 'Used Microsoft Surface Pro in excellent condition', 999.99, CURRENT_DATE, 12, 8, 2, 1, 12),
    (13, 3, 'Google Pixel 6', 'Brand new Google Pixel 6', 799.99, CURRENT_DATE, 13, 9, 1, 1, 13),
    (14, 2, 'Amazon Echo Dot', 'Amazon Echo Dot in good condition', 49.99, CURRENT_DATE, 14, 10, 3, 1, 14),
    (15, 1, 'Apple MacBook Pro', 'Used MacBook Pro in excellent condition', 1299.99, CURRENT_DATE, 15, 2, 2, 1, 15),
    (16, 4, 'Dell XPS 13', 'Brand new Dell XPS 13 laptop', 1199.99, CURRENT_DATE, 16, 11, 1, 1, 16),
    (17, 5, 'HP Spectre x360', 'Brand new HP Spectre x360 laptop', 999.99, CURRENT_DATE, 17, 12, 1, 1, 17),
    (18, 3, 'Lenovo ThinkPad X1 Carbon', 'Brand new Lenovo ThinkPad X1 Carbon laptop', 1499.99, CURRENT_DATE, 18, 13, 1, 1, 18),
    (19, 2, 'Asus ZenBook 14', 'Brand new Asus ZenBook 14 laptop', 899.99, CURRENT_DATE, 19, 14, 1, 1, 19),
    (20, 1, 'Acer Swift 3', 'Brand new Acer Swift 3 laptop', 699.99, CURRENT_DATE, 20, 15, 1, 1, 20),
    (21, 1, 'Xiaomi Mi 11', 'Brand new Xiaomi Mi 11 smartphone', 699.99, CURRENT_DATE, 21, 16, 1, 2, 21),
    (22, 2, 'Samsung Galaxy Watch 4', 'Brand new Samsung Galaxy Watch 4', 249.99, CURRENT_DATE, 22, 1, 1, 6, 22),
    (23, 3, 'Apple AirPods Pro', 'Brand new Apple AirPods Pro', 199.99, CURRENT_DATE, 23, 2, 1, 6, 23),
    (24, 4, 'Sony WH-1000XM4', 'Brand new Sony WH-1000XM4 headphones', 349.99, CURRENT_DATE, 24, 5, 1, 6, 24),
    (25, 5, 'Bose QuietComfort 45', 'Brand new Bose QuietComfort 45 headphones', 329.99, CURRENT_DATE, 25, 6, 1, 6, 25),
    (26, 1, 'JBL Flip 5', 'Brand new JBL Flip 5 portable speaker', 119.99, CURRENT_DATE, 26, 7, 1, 6, 26),
    (27, 2, 'Beats Pill+', 'Brand new Beats Pill+ portable speaker', 179.99, CURRENT_DATE, 27, 8, 1, 6, 27),
    (28, 3, 'GoPro HERO10 Black', 'Brand new GoPro HERO10 Black action camera', 499.99, CURRENT_DATE, 28, 9, 1, 6, 28),
    (29, 4, 'DJI Mini 2', 'Brand new DJI Mini 2 drone', 449.99, CURRENT_DATE, 29, 10, 1, 6, 29),
    (30, 5, 'Canon EOS R6', 'Brand new Canon EOS R6 camera', 2499.99, CURRENT_DATE, 30, 11, 1, 6, 30),
    (31, 1, 'Nikon Z6 II', 'Brand new Nikon Z6 II camera', 1999.99, CURRENT_DATE, 31, 12, 1, 6, 31),
    (32, 2, 'Fujifilm X-T4', 'Brand new Fujifilm X-T4 camera', 1699.99, CURRENT_DATE, 32, 13, 1, 6, 32),
    (33, 3, 'Panasonic Lumix GH5', 'Brand new Panasonic Lumix GH5 camera', 1499.99, CURRENT_DATE, 33, 14, 1, 6, 33),
    (34, 4, 'Olympus OM-D E-M1 Mark III', 'Brand new Olympus OM-D E-M1 Mark III camera', 1399.99, CURRENT_DATE, 34, 15, 1, 6, 34),
    (35, 5, 'Sigma fp L', 'Brand new Sigma fp L camera', 2499.99, CURRENT_DATE, 35, 16, 1, 6, 35),
    (36, 1, 'Razer Blade 15', 'Brand new Razer Blade 15 gaming laptop', 1999.99, CURRENT_DATE, 36, 1, 1, 1, 36),
    (37, 2, 'Alienware m15 R6', 'Brand new Alienware m15 R6 gaming laptop', 2299.99, CURRENT_DATE, 37, 2, 1, 1, 37),
    (38, 3, 'MSI GS66 Stealth', 'Brand new MSI GS66 Stealth gaming laptop', 1799.99, CURRENT_DATE, 38, 3, 1, 1, 38),
    (39, 4, 'ASUS ROG Zephyrus G14', 'Brand new ASUS ROG Zephyrus G14 gaming laptop', 1499.99, CURRENT_DATE, 39, 4, 1, 1, 39),
    (40, 5, 'HP Omen 15', 'Brand new HP Omen 15 gaming laptop', 1299.99, CURRENT_DATE, 40, 5, 1, 1, 40),
    (41, 1, 'Xbox Series X', 'Brand new Xbox Series X console', 499.99, CURRENT_DATE, 41, 6, 1, 1, 41),
    (42, 2, 'PlayStation 5 Digital Edition', 'Brand new PlayStation 5 Digital Edition console', 399.99, CURRENT_DATE, 42, 7, 1, 1, 42),
    (43, 3, 'Nintendo Switch OLED', 'Brand new Nintendo Switch OLED console', 349.99, CURRENT_DATE, 43, 8, 1, 1, 43),
    (44, 4, 'Oculus Quest 2', 'Brand new Oculus Quest 2 VR headset', 299.99, CURRENT_DATE, 44, 9, 1, 1, 44),
    (45, 5, 'Apple TV 4K', 'Brand new Apple TV 4K streaming device', 179.99, CURRENT_DATE, 45, 10, 1, 1, 45),
    (46, 1, 'Amazon Fire TV Stick 4K', 'Brand new Amazon Fire TV Stick 4K streaming device', 49.99, CURRENT_DATE, 46, 11, 1, 1, 46),
    (47, 2, 'Google Chromecast with Google TV', 'Brand new Google Chromecast with Google TV streaming device', 49.99, CURRENT_DATE, 47, 12, 1, 1, 47),
    (48, 3, 'Roku Ultra', 'Brand new Roku Ultra streaming device', 99.99, CURRENT_DATE, 48, 13, 1, 1, 48),
    (49, 4, 'NVIDIA Shield TV Pro', 'Brand new NVIDIA Shield TV Pro streaming device', 199.99, CURRENT_DATE, 49, 14, 1, 1, 49),
    (50, 5, 'Sonos Beam', 'Brand new Sonos Beam soundbar', 399.99, CURRENT_DATE, 50, 15, 1, 1, 50),
    (51, 1, 'JBL Flip 5', 'Brand new JBL Flip 5 portable speaker', 119.99, CURRENT_DATE, 51, 7, 1, 6, 26),
    (52, 2, 'Beats Pill+', 'Brand new Beats Pill+ portable speaker', 179.99, CURRENT_DATE, 52, 8, 1, 6, 27),
    (53, 3, 'GoPro HERO10 Black', 'Brand new GoPro HERO10 Black action camera', 499.99, CURRENT_DATE, 53, 9, 1, 6, 28),
    (54, 4, 'DJI Mini 2', 'Brand new DJI Mini 2 drone', 449.99, CURRENT_DATE, 54, 10, 1, 6, 29),
    (55, 5, 'Canon EOS R6', 'Brand new Canon EOS R6 camera', 2499.99, CURRENT_DATE, 55, 11, 1, 6, 30),
    (56, 1, 'Nikon Z6 II', 'Brand new Nikon Z6 II camera', 1999.99, CURRENT_DATE, 56, 12, 1, 6, 31),
    (57, 2, 'Fujifilm X-T4', 'Brand new Fujifilm X-T4 camera', 1699.99, CURRENT_DATE, 57, 13, 1, 6, 32),
    (58, 3, 'Panasonic Lumix GH5', 'Brand new Panasonic Lumix GH5 camera', 1499.99, CURRENT_DATE, 58, 14, 1, 6, 33),
    (59, 4, 'Olympus OM-D E-M1 Mark III', 'Brand new Olympus OM-D E-M1 Mark III camera', 1399.99, CURRENT_DATE, 59, 15, 1, 6, 34),
    (60, 5, 'Sigma fp L', 'Brand new Sigma fp L camera', 2499.99, CURRENT_DATE, 60, 16, 1, 6, 35),
    (61, 1, 'Razer Blade 15', 'Brand new Razer Blade', 1999.99, CURRENT_DATE, 61, 1, 1, 1, 36),
    (62, 2, 'Alienware m15 R6', 'Brand new Alienware m15 R6 gaming laptop', 2299.99, CURRENT_DATE, 62, 2, 1, 1, 37),
    (63, 3, 'MSI GS66 Stealth', 'Brand new MSI GS66 Stealth gaming laptop', 1799.99, CURRENT_DATE, 63, 3, 1, 1, 38),
    (64, 4, 'ASUS ROG Zephyrus G14', 'Brand new ASUS ROG Zephyrus G14 gaming laptop', 1499.99, CURRENT_DATE, 64, 4, 1, 1, 39),
    (65, 5, 'HP Omen 15', 'Brand new HP Omen 15 gaming laptop', 1299.99, CURRENT_DATE, 65, 5, 1, 1, 40),
    (66, 1, 'Xbox Series X', 'Brand new Xbox Series X console', 499.99, CURRENT_DATE, 66, 6, 1, 1, 41),
    (67, 2, 'PlayStation 5 Digital Edition', 'Brand new PlayStation 5 Digital Edition console', 399.99, CURRENT_DATE, 67, 7, 1, 1, 42),
    (68, 3, 'Nintendo Switch OLED', 'Brand new Nintendo Switch OLED console', 349.99, CURRENT_DATE, 68, 8, 1, 1, 43),
    (69, 4, 'Oculus Quest 2', 'Brand new Oculus Quest 2 VR headset', 299.99, CURRENT_DATE, 69, 9, 1, 1, 44),
    (70, 5, 'Apple TV 4K', 'Brand new Apple TV 4K streaming device', 179.99, CURRENT_DATE, 70, 10, 1, 1, 45),
    (71, 1, 'Amazon Fire TV Stick 4K', 'Brand new Amazon Fire TV Stick 4K streaming device', 49.99, CURRENT_DATE, 71, 11, 1, 1, 46),
    (72, 2, 'Google Chromecast with Google TV', 'Brand new Google Chromecast with Google TV streaming device', 49.99, CURRENT_DATE, 72, 12, 1, 1, 47),
    (73, 3, 'Roku Ultra', 'Brand new Roku Ultra streaming device', 99.99, CURRENT_DATE, 73, 13, 1, 1, 48),
    (74, 4, 'NVIDIA Shield TV Pro', 'Brand new NVIDIA Shield TV Pro streaming device', 199.99, CURRENT_DATE, 74, 14, 1, 1, 49),
    (75, 5, 'Sonos Beam', 'Brand new Sonos Beam soundbar', 399.99, CURRENT_DATE, 75, 15, 1, 1, 50),
    (76, 1, 'JBL Flip 5', 'Brand new JBL Flip 5 portable speaker', 119.99, CURRENT_DATE, 76, 7, 1, 6, 26),
    (77, 2, 'Beats Pill+', 'Brand new Beats Pill+ portable speaker', 179.99, CURRENT_DATE, 77, 8, 1, 6, 27),
    (78, 3, 'GoPro HERO10 Black', 'Brand new GoPro HERO10 Black action camera', 499.99, CURRENT_DATE, 78, 9, 1, 6, 28),
    (79, 4, 'DJI Mini 2', 'Brand new DJI Mini 2 drone', 449.99, CURRENT_DATE, 79, 10, 1, 6, 29),
    (80, 5, 'Canon EOS R6', 'Brand new Canon EOS R6 camera', 2499.99, CURRENT_DATE, 80, 11, 1, 6, 30),
    (81, 1, 'Nikon Z6 II', 'Brand new Nikon Z6 II camera', 1999.99, CURRENT_DATE, 81, 12, 1, 6, 31),
    (82, 2, 'Fujifilm X-T4', 'Brand new Fujifilm X-T4 camera', 1699.99, CURRENT_DATE, 82, 13, 1, 6, 32),
    (83, 3, 'Panasonic Lumix GH5', 'Brand new Panasonic Lumix GH5 camera', 1499.99, CURRENT_DATE, 83, 14, 1, 6, 33),
    (84, 4, 'Olympus OM-D E-M1 Mark III', 'Brand new Olympus OM-D E-M1 Mark III camera', 1399.99, CURRENT_DATE, 84, 15, 1, 6, 34),
    (85, 5, 'Sigma fp L', 'Brand new Sigma fp L camera', 2499.99, CURRENT_DATE, 85, 16, 1, 6, 35),
    (86, 1, 'Razer Blade 15', 'Brand new Razer Blade', 1999.99, CURRENT_DATE, 86, 1, 1, 1, 36),
    (87, 2, 'Alienware m15 R6', 'Brand new Alienware m15 R6 gaming laptop', 2299.99, CURRENT_DATE, 87, 2, 1, 1, 37),
    (88, 3, 'MSI GS66 Stealth', 'Brand new MSI GS66 Stealth gaming laptop', 1799.99, CURRENT_DATE, 88, 3, 1, 1, 38),
    (89, 4, 'ASUS ROG Zephyrus G14', 'Brand new ASUS ROG Zephyrus G14 gaming laptop', 1499.99, CURRENT_DATE, 89, 4, 1, 1, 39),
    (90, 5, 'HP Omen 15', 'Brand new HP Omen 15 gaming laptop', 1299.99, CURRENT_DATE, 90, 5, 1, 1, 40),
    (91, 1, 'Xbox Series X', 'Brand new Xbox Series X console', 499.99, CURRENT_DATE, 91, 6, 1, 1, 41),
    (92, 2, 'PlayStation 5 Digital Edition', 'Brand new PlayStation 5 Digital Edition console', 399.99, CURRENT_DATE, 92, 7, 1, 1, 42),
    (93, 3, 'Nintendo Switch OLED', 'Brand new Nintendo Switch OLED console', 349.99, CURRENT_DATE, 93, 8, 1, 1, 43),
    (94, 4, 'Oculus Quest 2', 'Brand new Oculus Quest 2 VR headset', 299.99, CURRENT_DATE, 94, 9, 1, 1, 44),
    (95, 5, 'Apple TV 4K', 'Brand new Apple TV 4K streaming device', 179.99, CURRENT_DATE, 95, 10, 1, 1, 45),
    (96, 1, 'Amazon Fire TV Stick 4K', 'Brand new Amazon Fire TV Stick 4K streaming device', 49.99, CURRENT_DATE, 96, 11, 1, 1, 46),
    (97, 2, 'Google Chromecast with Google TV', 'Brand new Google Chromecast with Google TV streaming device', 49.99, CURRENT_DATE, 97, 12, 1, 1, 47),
    (98, 3, 'Roku Ultra', 'Brand new Roku Ultra streaming device', 99.99, CURRENT_DATE, 98, 13, 1, 1, 48),
    (99, 4, 'NVIDIA Shield TV Pro', 'Brand new NVIDIA Shield TV Pro streaming device', 199.99, CURRENT_DATE, 99, 14, 1, 1, 49),
    (100, 5, 'Sonos Beam', 'Brand new Sonos Beam soundbar', 399.99, CURRENT_DATE, 100, 15, 1, 1, 50);

    -- Inserir dados de exemplo na tabela ProductCategory
    INSERT INTO ProductCategory (CategoryId, CategoryName)
    VALUES
    (1, 'Electronics'),
    (2, 'Clothing'),
    (3, 'Books'),
    (4, 'Furniture'),
    (5, 'Home Appliances'),
    (6, 'Jewelry'),
    (7, 'Toys'),
    (8, 'Sports Equipment'),
    (9, 'Automotive'),
    (10, 'Tools'),
    (11, 'Health & Beauty'),
    (12, 'Pet Supplies'),
    (13, 'Food & Beverages'),
    (14, 'Music'),
    (15, 'Movies'),
    (16, 'Video Games'),
    (17, 'Collectibles'),
    (18, 'Art'),
    (19, 'Crafts'),
    (20, 'Antiques');

    -- Inserir dados de exemplo na tabela ItemCategory pode ter mais do que uma categoria por item
    INSERT INTO ItemCategory (ItemId, CategoryId)
    VALUES
    (1, 1),
    (2, 2),
    (3, 1),
    (4, 2),
    (5, 2),
    (6, 1),
    (7, 6),
    (8, 1),
    (9, 2),
    (10, 1),
    (11, 1),(11, 2), 
    (12, 1),
    (13, 1),
    (14, 1),
    (15, 1),
    (16, 1),
    (17, 1),
    (18, 1),
    (19, 1),
    (20, 1),
    (21, 1),(21, 3),
    (22, 1),
    (23, 1),
    (24, 1),
    (25, 1),
    (26, 1),
    (27, 1),
    (28, 1),
    (29, 1),
    (30, 1),
    (31, 1),
    (32, 1),
    (33, 1),
    (34, 1),
    (35, 1),
    (36, 1),
    (37, 1),
    (38, 1),
    (39, 1),
    (40, 1),
    (41, 1),
    (42, 1),
    (43, 1),
    (44, 1),
    (45, 1),
    (46, 1),
    (47, 1),
    (48, 1),
    (49, 1),
    (50, 1),
    (51, 1),
    (52, 1),
    (53, 1),
    (54, 1),
    (55, 1),
    (56, 1),
    (57, 1),
    (58, 1),
    (59, 1),(59, 2),
    (60, 1),
    (61, 1),
    (62, 1),
    (63, 1),
    (64, 1),
    (65, 1),
    (66, 1),
    (67, 1),
    (68, 1),
    (69, 1),(69, 3),
    (70, 1),
    (71, 1),
    (72, 1),
    (73, 1),
    (74, 1),
    (75, 1),
    (76, 1),
    (77, 1),
    (78, 1),
    (79, 1),
    (80, 1),
    (81, 1),
    (82, 1),
    (83, 1),
    (84, 1),
    (85, 1),(85, 2),
    (86, 1),
    (87, 1),
    (88, 1),
    (89, 1),
    (90, 1),
    (91, 1),
    (92, 1),
    (93, 1),
    (94, 1),
    (95, 1),(95, 3),
    (96, 1),
    (97, 1),
    (98, 1),
    (99, 1),
    (100, 1);


    -- Inserir dados de exemplo na tabela ItemBrand
    INSERT INTO ItemBrand (BrandId, BrandName)
    VALUES
    (1, 'Samsung'),
    (2, 'Apple'),
    (3, 'Nike'),
    (4, 'Adidas'),
    (5, 'Sony'),
    (6, 'Rolex'),
    (7, 'LG'),
    (8, 'Microsoft'),
    (9, 'Google'),
    (10, 'Amazon'),
    (11, 'Dell'),
    (12, 'HP'),
    (13, 'Lenovo'),
    (14, 'Asus'),
    (15, 'Acer'),
    (16, 'Xiaomi'),
    (17, 'Fujifilm'),
    (18, 'Panasonic'),
    (19, 'Olympus'),
    (20, 'Sigma'),
    (21, 'Razer'),
    (22, 'Alienware'),
    (23, 'MSI'),
    (24, 'ASUS'),
    (25, 'HP'),
    (26, 'Xbox'),
    (27, 'PlayStation'),
    (28, 'Nintendo'),
    (29, 'Oculus'),
    (30, 'Apple TV'),
    (31, 'Amazon Fire TV'),
    (32, 'Google Chromecast'),
    (33, 'Roku'),
    (34, 'NVIDIA'),
    (35, 'Sonos'),
    (36, 'JBL'),
    (37, 'Beats'),
    (38, 'GoPro'),
    (39, 'DJI'),
    (40, 'Canon'),
    (41, 'Nikon'),
    (42, 'Fujifilm'),
    (43, 'Panasonic'),
    (44, 'Olympus'),
    (45, 'Sigma'),
    (46, 'Razer'),
    (47, 'Alienware'),
    (48, 'MSI'),
    (49, 'ASUS'),
    (50, 'HP'),
    (51, 'Xbox'),
    (52, 'PlayStation'),
    (53, 'Nintendo'),
    (54, 'Oculus'),
    (55, 'Apple TV'),
    (56, 'Amazon Fire TV'),
    (57, 'Google Chromecast'),
    (58, 'Roku'),
    (59, 'NVIDIA'),
    (60, 'Sonos'),
    (61, 'JBL'),
    (62, 'Beats'),
    (63, 'GoPro'),
    (64, 'DJI'),
    (65, 'Canon'),
    (66, 'Nikon'),
    (67, 'Fujifilm'),
    (68, 'Panasonic'),
    (69, 'Olympus'),
    (70, 'Sigma'),
    (71, 'Razer'),
    (72, 'Alienware'),
    (73, 'MSI'),
    (74, 'ASUS'),
    (75, 'HP'),
    (76, 'Xbox'),
    (77, 'PlayStation'),
    (78, 'Nintendo'),
    (79, 'Oculus'),
    (80, 'Apple TV'),
    (81, 'Amazon Fire TV'),
    (82, 'Google Chromecast'),
    (83, 'Roku'),
    (84, 'NVIDIA'),
    (85, 'Sonos'),
    (86, 'JBL'),
    (87, 'Beats'),
    (88, 'GoPro'),
    (89, 'DJI'),
    (90, 'Canon'),
    (91, 'Nikon'),
    (92, 'Fujifilm'),
    (93, 'Panasonic'),
    (94, 'Olympus'),
    (95, 'Sigma'),
    (96, 'Razer'),
    (97, 'Alienware'),
    (98, 'MSI'),
    (99, 'ASUS'),
    (100, 'HP');


    -- Inserir dados de exemplo na tabela ItemCondition
    INSERT INTO ItemCondition (ConditionId, ConditionName)
    VALUES
    (1, 'New'),
    (2, 'Used - Like New'),
    (3, 'Used - Good'),
    (4, 'Used - Fair'),
    (5, 'Bad'),
    (6, 'For Parts');

    -- Inserir dados de exemplo na tabela ItemSize
    INSERT INTO ItemSize (SizeId, SizeName)
    VALUES
    (1, 'Extra Small'),
    (2, 'Small'),
    (3, 'Medium'),
    (4, 'Large'),
    (5, 'Extra Large'),
    (6, 'One Size Fits All');

    -- Inserir dados de exemplo na tabela ItemModel
    INSERT INTO ItemModel (ModelId, ModelName)
    VALUES
    (1, 'Galaxy S20'),
    (2, 'Air Max 270'),
    (3, 'MacBook Pro'),
    (4, 'Air Force 1'),
    (5, 'Superstar'),
    (6, 'PlayStation 5'),
    (7, 'Submariner'),
    (8, 'iPhone 12'),
    (9, 'Yeezy Boost 350'),
    (10, 'QLED TV'),
    (11, 'Surface Pro'),
    (12, 'Pixel 6'),
    (13, 'Echo Dot'),
    (14, 'MacBook Pro'),
    (15, 'XPS 13'),
    (16, 'Spectre x360'),
    (17, 'ThinkPad X1 Carbon'),
    (18, 'ZenBook 14'),
    (19, 'Swift 3'),
    (20, 'Mi 11'),
    (21, 'Galaxy Watch 4'),
    (22, 'AirPods Pro'),
    (23, 'WH-1000XM4'),
    (24, 'QuietComfort 45'),
    (25, 'Flip 5'),
    (26, 'Pill+'),
    (27, 'HERO10 Black'),
    (28, 'Mini 2'),
    (29, 'EOS R6'),
    (30, 'Z6 II'),
    (31, 'X-T4'),
    (32, 'Lumix GH5'),
    (33, 'OM-D E-M1 Mark III'),
    (34, 'fp L'),
    (35, 'Blade 15'),
    (36, 'm15 R6'),
    (37, 'GS66 Stealth'),
    (38, 'ROG Zephyrus G14'),
    (39, 'Omen 15'),
    (40, 'Series X'),
    (41, 'PlayStation 5 Digital Edition'),
    (42, 'Switch OLED'),
    (43, 'Quest 2'),
    (44, 'TV 4K'),
    (45, 'Fire TV Stick 4K'),
    (46, 'Chromecast with Google TV'),
    (47, 'Roku Ultra'),
    (48, 'Shield TV Pro'),
    (49, 'Beam'),
    (50, 'Flip 5'),
    (51, 'Pill+'),
    (52, 'HERO10 Black'),
    (53, 'Mini 2'),
    (54, 'EOS R6'),
    (55, 'Z6 II'),
    (56, 'X-T4'),
    (57, 'Lumix GH5'),
    (58, 'OM-D E-M1 Mark III'),
    (59, 'fp L'),
    (60, 'Blade 15'),
    (61, 'm15 R6'),
    (62, 'GS66 Stealth'),
    (63, 'ROG Zephyrus G14'),
    (64, 'Omen 15'),
    (65, 'Series X'),
    (66, 'PlayStation 5 Digital Edition'),
    (67, 'Switch OLED'),
    (68, 'Quest 2'),
    (69, 'TV 4K'),
    (70, 'Fire TV Stick 4K'),
    (71, 'Chromecast with Google TV'),
    (72, 'Roku Ultra'),
    (73, 'Shield TV Pro'),
    (74, 'Beam'),
    (75, 'Flip 5'),
    (76, 'Pill+'),
    (77, 'HERO10 Black'),
    (78, 'Mini 2'),
    (79, 'EOS R6'),
    (80, 'Z6 II'),
    (81, 'X-T4'),
    (82, 'Lumix GH5'),
    (83, 'OM-D E-M1 Mark III'),
    (84, 'fp L'),
    (85, 'Blade 15'),
    (86, 'm15 R6'),
    (87, 'GS66 Stealth'),
    (88, 'ROG Zephyrus G14'),
    (89, 'Omen 15'),
    (90, 'Series X'),
    (91, 'PlayStation 5 Digital Edition'),
    (92, 'Switch OLED'),
    (93, 'Quest 2'),
    (94, 'TV 4K'),
    (95, 'Fire TV Stick 4K'),
    (96, 'Chromecast with Google TV'),
    (97, 'Roku Ultra'),
    (98, 'Shield TV Pro'),
    (99, 'Beam'),
    (100, 'Flip 5');


    -- Inserir dados de exemplo na tabela ItemImage
    INSERT INTO ItemImage (ItemId, ImageId)
    VALUES
    (1, 1),
    (2, 2),
    (3, 3),
    (4, 4),
    (5, 5),
    (6, 6),
    (7, 7),
    (8, 8),
    (9, 9),
    (10, 10),
    (11, 11),
    (12, 12),
    (13, 13),
    (14, 14),
    (15, 15),
    (16, 16),
    (17, 17),
    (18, 18),
    (19, 19),
    (20, 20),
    (21, 21),
    (22, 22),
    (23, 23),
    (24, 24),
    (25, 25),
    (26, 26),
    (27, 27),
    (28, 28),
    (29, 29),
    (30, 30),
    (31, 31),
    (32, 32),
    (33, 33),
    (34, 34),
    (35, 35),
    (36, 36),
    (37, 37),
    (38, 38),
    (39, 39),
    (40, 40),
    (41, 41),
    (42, 42),
    (43, 43),
    (44, 44),
    (45, 45),
    (46, 46),
    (47, 47),
    (48, 48),
    (49, 49),
    (50, 50),
    (51, 51),
    (52, 52),
    (53, 53),
    (54, 54),
    (55, 55),
    (56, 56),
    (57, 57),
    (58, 58),
    (59, 59),
    (60, 60),
    (61, 61),
    (62, 62),
    (63, 63),
    (64, 64),
    (65, 65),
    (66, 66),
    (67, 67),
    (68, 68),
    (69, 69),
    (70, 70),
    (71, 71),
    (72, 72),
    (73, 73),
    (74, 74),
    (75, 75),
    (76, 76),
    (77, 77),
    (78, 78),
    (79, 79),
    (80, 80),
    (81, 81),
    (82, 82),
    (83, 83),
    (84, 84),
    (85, 85),
    (86, 86),
    (87, 87),
    (88, 88),
    (89, 89),
    (90, 90),
    (91, 91),
    (92, 92),
    (93, 93),
    (94, 94),
    (95, 95),
    (96, 96),
    (97, 97),
    (98, 98),
    (99, 99),
    (100, 100);




    -- Inserir dados de exemplo na tabela ProductImage
    INSERT INTO ProductImage(ImageId,ImageUrl)
    VALUES
    (1, 'database/uploads/item_1/samsung-galaxy-s20-fe-5g-g781-128gb-dual-sim-lavanda.jpg'),
    (2, 'database/uploads/item_2/1-nike-air-max-270.jpg'),
    (3, 'database/uploads/item_3/Apple_16-inch-MacBook-Pro_111319_big.jpg.large.jpg'),
    (4, 'database/uploads/item_4/sapatilhas-air-force-1-07-1nfJ59.jpg'),
    (5, 'database/uploads/item_5/adidas-superstar-gore-tex-core-black-white-if6162-658e93bd76e79.jpg'),
    (6, 'database/uploads/item_6/125861_3_sony-playstation-5-standard-edition-825gb-ssd.jpg'),
    (7, 'database/uploads/item_7/m126619lb-0003_modelpage_front_facing_portrait.png'),
    (8, 'database/uploads/item_8/apple-iphone-12-128gb-azul.jpg'),
    (9, 'database/uploads/item_9/adidas-yeezy-boost-350-v2-bone-1-1000.webp'),
    (10, 'database/uploads/item_10/images.jpeg'),
    (11, 'database/uploads/item_11/1eeb021af7de0c9a84e0f122798aacd581879831.webp'),
    (12, 'database/uploads/item_12/Content-Card-Surface-Pro-9-EB-Angle.avif'),
    (13, 'database/uploads/item_13/google-pixel-6-pro-5g-12gb-128gb-dual-sim-negro.jpg'),
    (14, 'database/uploads/item_14/amazonalexaazulc_relogio_1024x.jpg'),
    (15, 'database/uploads/item_15/Apple-Macbook-Pro-13-M2-Space-Gray.jpg'),
    (16, 'database/uploads/item_16/images.jpeg'),
    (17, 'database/uploads/item_17/c08277538_1750x1285.avif'),
    (18, 'database/uploads/item_18/lenovo-laptops-x1-carbon-6th-gen-hero.avif'),
    (19, 'database/uploads/item_19/UP3404VA-73BOHDAP1.jpg'),
    (20, 'database/uploads/item_20/AcerSwift3SF313-53__1_.jpeg'),
    (21, 'database/uploads/item_21/Xiaomi-Mi-11-5G-Smartphone-6-81-Inch-12GB-256GB-Black-427111-0._w500_.jpg'),
    (22, 'database/uploads/item_22/SM-R865FZDAEUB_1.jpg'),
    (23, 'database/uploads/item_23/MQD83ZMA.jpg'),
    (24, 'database/uploads/item_24/61cm-9ZjI0L._AC_UF1000,1000_QL80_.jpg'),
    (25, 'database/uploads/item_25/572019_3_bose-quietcomfort-qc-45-auscultadores-bluetooth-com-microfone-noise-cancelling-black.jpg'),
    (26, 'database/uploads/item_31/z6IIe.jpg'),
    (27, 'database/uploads/item_27/beats-pill-.jpg'),
    (28, 'database/uploads/item_28/GoPro_News_HERO10_Black.jpg'),
    (29, 'database/uploads/item_29/1540-1.jpg'),
    (30, 'database/uploads/item_30/Canon-EOS-R6-lead-01.jpeg'),
    (31, 'database/uploads/item_31/z6IIe.jpg'),
    (32, 'database/uploads/item_32/11540-1.jpg'),
    (33, 'database/uploads/item_33/panasonic_lumix_gh5_review_8c9cd6ffa9b02044a7a3327bc82c5649.jpg'),
    (34, 'database/uploads/item_34/design-medium.jpg'),
    (35, 'database/uploads/item_35/fp_L_tipa_1.jpg'),
    (36, 'database/uploads/item_36/4zu3_Razer_Blade_15_Advanced_Model_2020.jpg'),
    (37, 'database/uploads/item_37/71J1lHBTo3L._AC_UF894,1000_QL80_.jpg'),
    (38, 'database/uploads/item_38/MSIGS66_-10SF-Stealth__1__05.JPG'),
    (39, 'database/uploads/item_39/ASUSROGZephyrusG14-GA402__1_.jpg'),
    (40, 'database/uploads/item_40/61IEiK7OODL._AC_SL1500__02.jpg'),
    (41, 'database/uploads/item_41/shutterstock_1886336365_Easy-Resize.com_.jpg'),
    (42, 'database/uploads/item_42/6566040_sd.jpg'),
    (43, 'database/uploads/item_43/CI_NSwitch_main.jpg'),
    (44, 'database/uploads/item_44/www.ubuy.com.jpeg'),
    (45, 'database/uploads/item_45/apple-tv-4k-hero-select-202210_FMT_WHH.jpeg'),
    (46, 'database/uploads/item_46/amazon_fire_tv_stick_4k_max_mando_01_l.jpg'),
    (47, 'database/uploads/item_47/GA03131IT.jpg'),
    (48, 'database/uploads/item_48/71zgNLOckwL.jpg'),
    (49, 'database/uploads/item_49/629048_63_nvidia-shield-tv-pro-2019-4k-hdr.jpg'),
    (50, 'database/uploads/item_50/sonos-beam-gen-2-dolby-atmos-airplay-21.jpeg'),
    (51, 'database/uploads/item_51/da2f726f-aeed-4547-a9b1-76a0ec0061f0-1_75fafcf9-f07b-4b0f-92db-b770d81c198e.jpg'),
    (52, 'database/uploads/item_52/Coluna-Beats-Beats-Pill-Preto.jpg'),
    (53, 'database/uploads/item_53/images.jpeg'),
    (54, 'database/uploads/item_54/0c7373a3a5fb102f9c36461905e4b44b.jpg'),
    (55, 'database/uploads/item_55/eos-r6-mark-ii-header-clean_c465f3beb4c34dbfbf08a07e44ea985d.jpeg'),
    (56, 'database/uploads/item_56/1602636583_15987211-281ea1e42d6a3ee13916503744172964-1024-1024.jpg'),
    (57, 'database/uploads/item_57/41aElEKSrpL._AC_.jpg'),
    (58, 'database/uploads/item_58/3c381ab07daa1d7174fccafe777f511fd01c8d13.jpg'),
    (59, 'database/uploads/item_59/AW-11-04-24-21083.jpg'),
    (60, 'database/uploads/item_60/Sigma-fp-L-top-02.jpeg'),
    (61, 'database/uploads/item_61/71jhDKpPu7L._AC_UF894,1000_QL80_.jpg'),
    (62, 'database/uploads/item_62/images.jpeg'),
    (63, 'database/uploads/item_63/1520-1.jpg'),
    (64, 'database/uploads/item_64/section9img.png'),
    (65, 'database/uploads/item_65/OMEN-15-1.jpg'),
    (66, 'database/uploads/item_66/spongebob_xbox.0.jpg'),
    (67, 'database/uploads/item_67/719C0kp2XyL._AC_UF894,1000_QL80_.jpg'),
    (68, 'database/uploads/item_68/Myheroacademia-nintendoswitcholedskin-v2-2.jpg'),
    (69, 'database/uploads/item_69/61DoWjG+pEL.jpg'),
    (70, 'database/uploads/item_70/ATV4-SLATE.jpg'),
    (71, 'database/uploads/item_71/51hD9Hkt50L._AC_UF894,1000_QL80_.jpg'),
    (72, 'database/uploads/item_72/google-chromecast-google-tv-review-2021-05.jpg'),
    (73, 'database/uploads/item_73/71zgNLOckwL.jpg'),
    (74, 'database/uploads/item_74/650_1200.jpg'),
    (75, 'database/uploads/item_75/Sonos-Beam-Gen-2-hero-3.jpg'),
    (76, 'database/uploads/item_76/761-5122c2b1fa0d4a6d6d16864314726448-1024-1024.jpg'),
    (77, 'database/uploads/item_77/4191eGBO0VL._AC_.jpg'),
    (78, 'database/uploads/item_78/design-medium.jpg'),
    (79, 'database/uploads/item_79/dji-mini-3-apenas-drone.jpg'),
    (80, 'database/uploads/item_80/s-l1600.jpg'),
    (81, 'database/uploads/item_81/91RhH0Ptb4L.jpg'),
    (82, 'database/uploads/item_82/s-l400.jpg'),
    (83, 'database/uploads/item_83/b21-4f6800bfaf8c0d722a16880765237145-1024-1024.jpg'),
    (84, 'database/uploads/item_84/800x.jpg'),
    (85, 'database/uploads/item_85/Sigma-fp-L-Leica-35mm-Summicron-M-Version-4-1.jpg'),
    (86, 'database/uploads/item_86/61koolCX9sL._AC_UF894,1000_QL80_.jpg'),
    (87, 'database/uploads/item_87/61+ctUXYQhL._AC_UF894,1000_QL80_.jpg'),
    (88, 'database/uploads/item_88/s-l1200.jpg'),
    (89, 'database/uploads/item_89/61gRPLJyeXL.jpg'),
    (90, 'database/uploads/item_90/images.jpeg'),
    (91, 'database/uploads/item_91/Screenshot from 2024-05-19 23-20-07.png'),
    (92, 'database/uploads/item_92/SPS5D825GBCR003_l.jpg'),
    (93, 'database/uploads/item_93/61TcJjR0YWL.jpg'),
    (94, 'database/uploads/item_94/51ve6ZxOApL.jpg'),
    (95, 'database/uploads/item_95/ATV4-RETRO-HOR.jpg'),
    (96, 'database/uploads/item_96/51tHrqq3p5L._AC_UF894,1000_QL80_.jpg'),
    (97, 'database/uploads/item_97/6425976_sd.jpg'),
    (98, 'database/uploads/item_98/61VEOqemK+S._AC_UF894,1000_QL80_.jpg'),
    (99, 'database/uploads/item_99/615-iviiW1L._AC_UF894,1000_QL80_.jpg'),
    (100, 'database/uploads/item_100/71zOGDc-XsL._AC_UF1000,1000_QL80_.jpg');
        

    

    -- Inserir dados de exemplo na tabela Payment
    INSERT INTO Payment (PaymentId, BuyerId, SellerId, ItemId, Address, City, District, Country, PostalCode, PaymentDate)
    VALUES
    (1, 2, 1, 1, '123 Main St', 'Anytown', 'Anydistrict', 'AnyCountry', '12345', CURRENT_DATE),
    (2, 3, 2, 3, '456 Oak St', 'Othertown', 'Otherdistrict', 'OtherCountry', '54321', CURRENT_DATE),
    (3, 1, 2, 4, '789 Elm St', 'Somewhere', 'Somedistrict', 'SomeCountry', '67890', CURRENT_DATE),
    (4, 2, 3, 5, '101 Pine St', 'Anywhere', 'Anydistrict', 'AnyCountry', '54321', CURRENT_DATE);

    -- Inserir dados de exemplo na tabela Review
    INSERT INTO Review (ReviewId, UserId, ItemId, Rating, Comment, ReviewDate)
    VALUES
    (1, 2, 5, 3.5, 'Great phone, fast shipping', CURRENT_DATE),
    (2, 5, 5, 3.0, 'Good laptop, fast delivery', CURRENT_DATE),
    (3, 1, 5, 4.0, 'Shoes are nice, but took a while to arrive', CURRENT_DATE),
    (4, 4, 5, 5.0, 'Great sneakers, fast shipping', CURRENT_DATE);