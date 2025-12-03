<?php
include 'config.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Logout functionality
if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Notes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f5f5f5;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            font-size: 24px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn {
            padding: 8px 15px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .welcome {
            text-align: center;
            margin-bottom: 40px;
        }
        .welcome h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .welcome p {
            color: #666;
        }
        .options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .option-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .option-card:hover {
            transform: translateY(-5px);
        }
        .option-card h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .option-card p {
            color: #666;
            margin-bottom: 20px;
        }
        .btn-primary {
            display: inline-block;
            padding: 12px 25px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestion des Notes</h1>
        <div class="user-info">
            <span>Bonjour, <?php echo $_SESSION['user_name']; ?></span>
            <a href="?logout" class="btn">Déconnexion</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome">
            <h2>Bienvenue dans votre espace personnel</h2>
            <p>Gérez vos notes et calculez vos moyennes facilement</p>
        </div>
        
        <div class="options">
            <div class="option-card">
                <h3>Calculer la moyenne</h3>
                <p>Saisir vos notes et voir votre moyenne générale calculée automatiquement.</p>
                <a href="calculate.php" class="btn-primary">Calculer</a>
            </div>
            
            <div class="option-card">
                <h3>Changer une note</h3>
                <p>Modifier une note existante pour recalculer instantanément vos moyennes.</p>
                <a href="update.php" class="btn-primary">Modifier</a>
            </div>
        </div>
    </div>
</body>
</html>