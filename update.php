<?php
include 'config.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all ECUEs with grades for the current user
$sql = "
    SELECT e.id, e.nom, e.coef, u.code as ue_code, u.nom as ue_nom,
           g.cc, g.tp, g.examen
    FROM ecues e
    JOIN ues u ON e.ue_id = u.id
    LEFT JOIN grades g ON e.id = g.ecue_id AND g.user_id = :user_id
    ORDER BY u.code, e.id
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$ecues = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $ecue_id = $_POST['ecue_id'];
    $cc = $_POST['cc'] !== '' && $_POST['cc'] >= 0 && $_POST['cc'] <= 20 ? $_POST['cc'] : null;
    $tp = $_POST['tp'] !== '' && $_POST['tp'] >= 0 && $_POST['tp'] <= 20 ? $_POST['tp'] : null;
    $examen = $_POST['examen'] !== '' && $_POST['examen'] >= 0 && $_POST['examen'] <= 20 ? $_POST['examen'] : null;
    
    // Check if grade record exists
    $sql = "SELECT id FROM grades WHERE user_id = :user_id AND ecue_id = :ecue_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':ecue_id', $ecue_id, PDO::PARAM_STR);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        // Update existing grade
        $sql = "UPDATE grades SET cc = :cc, tp = :tp, examen = :examen WHERE user_id = :user_id AND ecue_id = :ecue_id";
    } else {
        // Insert new grade
        $sql = "INSERT INTO grades (user_id, ecue_id, cc, tp, examen) VALUES (:user_id, :ecue_id, :cc, :tp, :examen)";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':ecue_id', $ecue_id, PDO::PARAM_STR);
    $stmt->bindParam(':cc', $cc);
    $stmt->bindParam(':tp', $tp);
    $stmt->bindParam(':examen', $examen);
    
    if($stmt->execute()) {
        $success = "Note mise à jour avec succès.";
        // Refresh the page to show updated data
        header("Location: update.php?success=1");
        exit;
    } else {
        $error = "Erreur lors de la mise à jour de la note.";
    }
}

// Check for success message
if(isset($_GET['success'])) {
    $success = "Note mise à jour avec succès.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer une note - Gestion des Notes</title>
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
        .nav {
            display: flex;
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
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .page-title {
            text-align: center;
            margin-bottom: 30px;
        }
        .page-title h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .page-title p {
            color: #666;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        .ecue-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .ecue-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .ecue-card:hover {
            transform: translateY(-5px);
        }
        .ecue-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .ecue-ue {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .ecue-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        .ecue-coef {
            font-size: 14px;
            color: #667eea;
            font-weight: 500;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input:focus {
            border-color: #667eea;
            outline: none;
        }
        .current-grades {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .current-grades h4 {
            color: #333;
            margin-bottom: 10px;
        }
        .grade-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .grade-label {
            color: #666;
        }
        .grade-value {
            font-weight: 500;
            color: #333;
        }
        .btn-primary {
            display: block;
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
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
            .nav {
                justify-content: center;
            }
            .ecue-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestion des Notes</h1>
        <div class="nav">
            <a href="dashboard.php" class="btn">Tableau de Bord</a>
            <a href="calculate.php" class="btn">Calculer la moyenne</a>
            <a href="?logout" class="btn">Déconnexion</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-title">
            <h2>Changer une note</h2>
            <p>Modifier une note existante pour recalculer instantanément vos moyennes</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="ecue-list">
            <?php foreach($ecues as $ecue): 
                // Calculate current average
                $moyenne = null;
                if($ecue['cc'] !== null && $ecue['tp'] !== null && $ecue['examen'] !== null) {
                    $moyenne = round($ecue['cc'] * 0.2 + $ecue['tp'] * 0.1 + $ecue['examen'] * 0.7, 2);
                }
            ?>
            <div class="ecue-card">
                <div class="ecue-header">
                    <div class="ecue-ue"><?php echo $ecue['ue_code']; ?></div>
                    <div class="ecue-name"><?php echo $ecue['nom']; ?></div>
                    <div class="ecue-coef">Coefficient: <?php echo $ecue['coef']; ?></div>
                </div>
                
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="ecue_id" value="<?php echo $ecue['id']; ?>">
                    
                    <div class="form-group">
                        <label for="cc_<?php echo $ecue['id']; ?>">CC (20%)</label>
                        <input type="number" id="cc_<?php echo $ecue['id']; ?>" name="cc" 
                               value="<?php echo $ecue['cc'] !== null ? $ecue['cc'] : ''; ?>" 
                               min="0" max="20" step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="tp_<?php echo $ecue['id']; ?>">TP (10%)</label>
                        <input type="number" id="tp_<?php echo $ecue['id']; ?>" name="tp" 
                               value="<?php echo $ecue['tp'] !== null ? $ecue['tp'] : ''; ?>" 
                               min="0" max="20" step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="examen_<?php echo $ecue['id']; ?>">Examen (70%)</label>
                        <input type="number" id="examen_<?php echo $ecue['id']; ?>" name="examen" 
                               value="<?php echo $ecue['examen'] !== null ? $ecue['examen'] : ''; ?>" 
                               min="0" max="20" step="0.01">
                    </div>
                    
                    <?php if($ecue['cc'] !== null || $ecue['tp'] !== null || $ecue['examen'] !== null): ?>
                    <div class="current-grades">
                        <h4>Notes actuelles</h4>
                        <div class="grade-item">
                            <span class="grade-label">CC:</span>
                            <span class="grade-value"><?php echo $ecue['cc'] !== null ? $ecue['cc'] : '-'; ?></span>
                        </div>
                        <div class="grade-item">
                            <span class="grade-label">TP:</span>
                            <span class="grade-value"><?php echo $ecue['tp'] !== null ? $ecue['tp'] : '-'; ?></span>
                        </div>
                        <div class="grade-item">
                            <span class="grade-label">Examen:</span>
                            <span class="grade-value"><?php echo $ecue['examen'] !== null ? $ecue['examen'] : '-'; ?></span>
                        </div>
                        <?php if($moyenne !== null): ?>
                        <div class="grade-item">
                            <span class="grade-label">Moyenne:</span>
                            <span class="grade-value"><?php echo $moyenne; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn-primary">Mettre à jour</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>