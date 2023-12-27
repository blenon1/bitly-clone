<?php 
    //Is received shortcut
    if (isset($_GET['q'])) {
        //variable
        $shortcut = htmlspecialchars($_GET['q']);
        //is really shortcut
        $request = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');
        $request->execute(array($shortcut));

        while($result = $request->fetch()) {
            if($result['x'] != 1) {
                header('location: ../error&message=Adresse url non connue');
                exit();
            }
        }

        //redirection
        $request = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
        $request->execute(array($shortcut));

        while($result = $request->fetch()) {
            header('location: '.$result['url']);
            exit(); 
        }
    }

   
    $bdd = new PDO('mysql:host=localhost;dbname=bitly_db', 'root', '');
    if(isset($_POST['url']) && isset($_POST['button'])) {
        //instancier la variable
        $url = $_POST['url'];
        //verification du URL
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            //Pas un lien
            header('Location: ../?error=true&message=Adresse url invalide 😩');
            exit();
        }
        //shortcut
        $shortcut = crypt($url, rand());
        //verification si le lien avais deja ete envoyer
        $request = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
        $request->execute(array($url));

        while($result = $request->fetch()) {
            if($result['x'] != 0) {
                header('Location: ../?error=true&message= Adresse url deja raccourcie !!!');
                exit();
            }
        }
        //Envoi url
        $request = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES(?, ?)');
        $request->execute(array($url, $shortcut));

         header('Location: ../?short='.$shortcut);
         exit();

    }
    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/favico.png" type="image/x-icon">
    <link rel="stylesheet" href="styles.css">
    <title>Bitly</title>
</head>

<body>
    <header>
        <img class="title-logo" src="./assets/logo.png" alt="">
        <div class="welcome-text">
            <h1>une url longue ? raccourcissez là</h1>
            <p>Largement meilleur et plus court que les autres.</p>
        </div>
        <div class="search-bar">
            <form method="post">
                <input type="url" placeholder="Coller votre lien a raccourcir" name="url">
                <input type="submit" name="button" value="raccourcir">
            </form>

            <?php if(isset($_GET['error']) && isset($_GET['message'])) { ?>
                <div class="display-shortcut">
                    <div id="result">
                        <b>
                            <?php echo htmlspecialchars($_GET['message']); ?>
                        </b>
                    </div>
                </div>
            <?php } else if(isset($_GET['short'])) { ?>
            <div class="display-shortcut">
                <div id="result">
                    <b>URL RACCOURCIE : </b>
                    http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>
                </div>
            </div>
            <?php } ?>
        </div>
    </header>
    <main>
        <section>
            <h2>ces marques nous font confiance</h2>
            <div class="images-container">
                <img class="images" src="./assets/1.png" alt="">
                <img class="images" src="./assets/2.png" alt="">
                <img class="images" src="./assets/3.png" alt="">
                <img class="images" src="./assets/4.png" alt="">
            </div>
        </section>

    </main>

    <footer>
        <img class="footer-img" src="./assets/logo2.png" alt="">
        <p>2023 © Bitly</p>
        <div class="contact">
            <a href="#">Contact</a>
            <p>.</p>
            <a href="#">A Propos</a>
        </div>
    </footer>

</body>

</html>