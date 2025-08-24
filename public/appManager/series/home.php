<?php
// Requêtes pour récupérer les totaux
$totalSeries = 5;
$totalFilms = 7;
$totalActeurs = 65;
$totalUtilisateurs = 5;
?>
<?php include '../../../includes/header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EvenProd</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --danger: #f72585;
            --warning: #f8961e;
            --info: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header-home {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .header-home h1 {
            color: var(--primary);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }

        .card:nth-child(1)::before { background: var(--primary); }
        .card:nth-child(2)::before { background: var(--info); }
        .card:nth-child(3)::before { background: var(--warning); }
        .card:nth-child(4)::before { background: var(--danger); }

        .card-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-info {
            flex: 1;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .card:nth-child(1) .card-icon { background: rgba(67, 97, 238, 0.15); color: var(--primary); }
        .card:nth-child(2) .card-icon { background: rgba(72, 149, 239, 0.15); color: var(--info); }
        .card:nth-child(3) .card-icon { background: rgba(248, 150, 30, 0.15); color: var(--warning); }
        .card:nth-child(4) .card-icon { background: rgba(247, 37, 133, 0.15); color: var(--danger); }

        .card h2 {
            font-size: 32px;
            margin: 10px 0 5px;
            font-weight: 700;
        }

        .card p {
            color: var(--gray);
            font-size: 14px;
            font-weight: 500;
        }

        .card-trend {
            display: flex;
            align-items: center;
            margin-top: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        .card-trend.up { color: #2ecc71; }
        .card-trend.down { color: #e74c3c; }

        .recent-activities {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--card-shadow);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 16px;
        }

        .activity-content {
            flex: 1;
        }

        .activity-content p {
            margin: 0;
            font-size: 14px;
        }

        .activity-time {
            color: var(--gray);
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            .header-home {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="header-home">
            <h1><i class="fas fa-chart-line"></i> Dashboard EvenProd</h1>
            <div class="header-actions">
                <!-- Vous pouvez ajouter des boutons d'action ici -->
            </div>
        </div>
        
        <div class="dashboard">
            <a href="#" class="card">
                <div class="card-content">
                    <div class="card-info">
                        <p>Total Séries</p>
                        <h2><?php echo $totalSeries; ?></h2>
                        <div class="card-trend up">
                            <i class="fas fa-arrow-up"></i> 12% depuis le mois dernier
                        </div>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-tv"></i>
                    </div>
                </div>
            </a>
            
            <a href="#" class="card">
                <div class="card-content">
                    <div class="card-info">
                        <p>Total Films</p>
                        <h2><?php echo $totalFilms; ?></h2>
                        <div class="card-trend up">
                            <i class="fas fa-arrow-up"></i> 8% depuis le mois dernier
                        </div>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-film"></i>
                    </div>
                </div>
            </a>
            
            <a href="<?php echo $url_base; ?>public/appManager/acteur/acteurs.php" class="card">
                <div class="card-content">
                    <div class="card-info">
                        <p>Total Acteurs</p>
                        <h2><?php echo $totalActeurs; ?></h2>
                        <div class="card-trend up">
                            <i class="fas fa-arrow-up"></i> 5% depuis le mois dernier
                        </div>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-theater-masks"></i>
                    </div>
                </div>
            </a>
            
            <a href="#" class="card">
                <div class="card-content">
                    <div class="card-info">
                        <p>Total Utilisateurs</p>
                        <h2><?php echo $totalUtilisateurs; ?></h2>
                        <div class="card-trend down">
                            <i class="fas fa-arrow-down"></i> 2% depuis le mois dernier
                        </div>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="recent-activities">
            <h2 class="section-title"><i class="fas fa-history"></i> Activités Récentes</h2>
            <ul class="activity-list">
                <li class="activity-item">
                    <div class="activity-icon" style="background-color: rgba(67, 97, 238, 0.15); color: var(--primary);">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="activity-content">
                        <p>Nouvelle série "Stranger Things" ajoutée</p>
                        <span class="activity-time">Il y a 2 heures</span>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon" style="background-color: rgba(72, 149, 239, 0.15); color: var(--info);">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                        <p>Nouvel acteur "Tom Hanks" ajouté</p>
                        <span class="activity-time">Il y a 5 heures</span>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon" style="background-color: rgba(248, 150, 30, 0.15); color: var(--warning);">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="activity-content">
                        <p>Film "Inception" modifié</p>
                        <span class="activity-time">Hier à 15:30</span>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon" style="background-color: rgba(247, 37, 133, 0.15); color: var(--danger);">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="activity-content">
                        <p>Nouvel utilisateur inscrit</p>
                        <span class="activity-time">23 Oct 2023</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <script>
        // Animation simple pour les chiffres
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.card h2');
            
            counters.forEach(counter => {
                const target = +counter.innerText;
                let count = 0;
                const duration = 1500; // en ms
                const increment = target / (duration / 16);
                
                const updateCount = () => {
                    if (count < target) {
                        count += increment;
                        counter.innerText = Math.ceil(count);
                        setTimeout(updateCount, 16);
                    } else {
                        counter.innerText = target;
                    }
                };
                
                updateCount();
            });
        });
    </script>
</body>
</html>