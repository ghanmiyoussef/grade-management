<?php
include 'config.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach($_POST['grades'] as $ecue_id => $grades) {
        // Validate grades (0-20)
        $cc = isset($grades['cc']) && $grades['cc'] >= 0 && $grades['cc'] <= 20 ? $grades['cc'] : null;
        $tp = isset($grades['tp']) && $grades['tp'] >= 0 && $grades['tp'] <= 20 ? $grades['tp'] : null;
        $examen = isset($grades['examen']) && $grades['examen'] >= 0 && $grades['examen'] <= 20 ? $grades['examen'] : null;
        
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
        $stmt->execute();
    }
    
    // Redirect to prevent form resubmission
    header("Location: calculate.php");
    exit;
}

// Get all UEs and ECUEs with grades
$sql = "
    SELECT u.id as ue_id, u.code as ue_code, u.nom as ue_nom, u.credit,
           e.id as ecue_id, e.nom as ecue_nom, e.coef,
           g.cc, g.tp, g.examen
    FROM ues u
    JOIN ecues e ON u.id = e.ue_id
    LEFT JOIN grades g ON e.id = g.ecue_id AND g.user_id = :user_id
    ORDER BY u.code, e.id
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize data by UE
$ues = [];
foreach($results as $row) {
    $ue_id = $row['ue_id'];
    if(!isset($ues[$ue_id])) {
        $ues[$ue_id] = [
            'code' => $row['ue_code'],
            'nom' => $row['ue_nom'],
            'credit' => $row['credit'],
            'ecues' => []
        ];
    }
    
    // Calculate ECUE average
    $moyenne_ecue = null;
    if($row['cc'] !== null && $row['tp'] !== null && $row['examen'] !== null) {
        $moyenne_ecue = round($row['cc'] * 0.2 + $row['tp'] * 0.1 + $row['examen'] * 0.7, 2);
    }
    
    $ues[$ue_id]['ecues'][] = [
        'id' => $row['ecue_id'],
        'nom' => $row['ecue_nom'],
        'coef' => $row['coef'],
        'cc' => $row['cc'],
        'tp' => $row['tp'],
        'examen' => $row['examen'],
        'moyenne' => $moyenne_ecue
    ];
}

// Calculate UE averages and overall average
$total_credits = 0;
$total_weighted_average = 0;

foreach($ues as &$ue) {
    $total_coef = 0;
    $weighted_sum = 0;
    $ue_has_grades = false;
    
    foreach($ue['ecues'] as $ecue) {
        if($ecue['moyenne'] !== null) {
            $total_coef += $ecue['coef'];
            $weighted_sum += $ecue['moyenne'] * $ecue['coef'];
            $ue_has_grades = true;
        }
    }
    
    if($ue_has_grades && $total_coef > 0) {
        $ue['moyenne'] = round($weighted_sum / $total_coef, 2);
        $total_credits += $ue['credit'];
        $total_weighted_average += $ue['moyenne'] * $ue['credit'];
    } else {
        $ue['moyenne'] = null;
    }
}

$moyenne_generale = $total_credits > 0 ? round($total_weighted_average / $total_credits, 2) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculer la moyenne - Gestion des Notes</title>
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
            max-width: 1200px;
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
        .ue-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .ue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .ue-title {
            font-size: 20px;
            color: #333;
        }
        .ue-credit {
            color: #666;
            font-weight: 500;
        }
        .ue-average {
            font-size: 18px;
            font-weight: 600;
            color: #667eea;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        tr:hover {
            background: #f8f9fa;
        }
        input {
            width: 80px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        input:focus {
            border-color: #667eea;
            outline: none;
        }
        .moyenne {
            font-weight: 600;
            color: #333;
        }
        .submit-section {
            text-align: center;
            margin-top: 30px;
        }
        .btn-primary {
            padding: 12px 30px;
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
        .overall-average {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .overall-average h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .overall-average-value {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
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
            table {
                font-size: 14px;
            }
            th, td {
                padding: 8px 10px;
            }
            input {
                width: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestion des Notes</h1>
        <div class="nav">
            <a href="dashboard.php" class="btn">Tableau de Bord</a>
            <a href="?logout" class="btn">Déconnexion</a>
        </div>
    </div>
    
    <div class="container">
        <div class="page-title">
            <h2>Calculer la moyenne</h2>
            <p>Saisir vos notes et voir votre moyenne générale calculée automatiquement</p>
        </div>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <?php foreach($ues as $ue_id => $ue): ?>
            <div class="ue-section">
                <div class="ue-header">
                    <div>
                        <h3 class="ue-title"><?php echo $ue['code'] . ' - ' . $ue['nom']; ?></h3>
                        <span class="ue-credit">Crédits: <?php echo $ue['credit']; ?></span>
                    </div>
                    <?php if($ue['moyenne'] !== null): ?>
                        <div class="ue-average">Moyenne UE: <?php echo $ue['moyenne']; ?></div>
                    <?php endif; ?>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ECUE</th>
                            <th>COEF</th>
                            <th>CC (20%)</th>
                            <th>TP (10%)</th>
                            <th>EXAMEN (70%)</th>
                            <th>MOYENNE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ue['ecues'] as $ecue): ?>
                        <tr>
                            <td style="text-align: left;"><?php echo $ecue['nom']; ?></td>
                            <td><?php echo $ecue['coef']; ?></td>
                            <td>
                                <input type="number" name="grades[<?php echo $ecue['id']; ?>][cc]" 
                                       value="<?php echo $ecue['cc'] !== null ? $ecue['cc'] : ''; ?>" 
                                       min="0" max="20" step="0.01">
                            </td>
                            <td>
                                <input type="number" name="grades[<?php echo $ecue['id']; ?>][tp]" 
                                       value="<?php echo $ecue['tp'] !== null ? $ecue['tp'] : ''; ?>" 
                                       min="0" max="20" step="0.01">
                            </td>
                            <td>
                                <input type="number" name="grades[<?php echo $ecue['id']; ?>][examen]" 
                                       value="<?php echo $ecue['examen'] !== null ? $ecue['examen'] : ''; ?>" 
                                       min="0" max="20" step="0.01">
                            </td>
                            <td class="moyenne">
                                <?php echo $ecue['moyenne'] !== null ? $ecue['moyenne'] : '-'; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
            
            <div class="submit-section">
                <button type="submit" class="btn-primary">Enregistrer les notes</button>
            </div>
        </form>
        
        <?php if($moyenne_generale !== null): ?>
        <div class="overall-average">
            <h3>Moyenne Générale</h3>
            <div class="overall-average-value"><?php echo $moyenne_generale; ?></div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>