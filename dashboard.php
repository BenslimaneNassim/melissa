<?php
try {
	$myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
	$myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>');
}

// Préparation de la requête SQL
$sql = "SELECT COUNT(*) as total_enseignants FROM enseignant";

try {
	// Exécution de la requête SQL
	$stmt = $myproject->query($sql);
	// Récupération du nombre d'enseignants
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_enseignants = $row['total_enseignants'];
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur lors de l\'exécution de la requête : ' . $e->getMessage() . '</p>');
}

// Préparation de la requête SQL pour compter le nombre de salles
$sqlSalle = "SELECT COUNT(*) as total_salles FROM lieu";

try {
	// Exécution de la requête SQL
	$stmt = $myproject->query($sqlSalle);
	// Récupération du nombre de salles
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_salles = $row['total_salles'];
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur lors de l\'exécution de la requête : ' . $e->getMessage() . '</p>');
}


// Préparation de la requête SQL pour compter le nombre de salles
$sqlModule = "SELECT COUNT(*) as total_module FROM module";

try {
	// Exécution de la requête SQL
	$stmt = $myproject->query($sqlModule);
	// Récupération du nombre de salles
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$total_module = $row['total_module'];
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur lors de l\'exécution de la requête : ' . $e->getMessage() . '</p>');
}




// Préparation de la requête SQL
$sqlGraphe = "SELECT grade, COUNT(*) as total FROM enseignant GROUP BY grade";

try {
	// Exécution de la requête SQL
	$stmt = $myproject->query($sqlGraphe);
	// Récupération des données dans un tableau associatif
	$gradesCount = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur lors de l\'exécution de la requête : ' . $e->getMessage() . '</p>');
}

// Convertir les données en format compatible avec Chart.js
$grades = array_column($gradesCount, 'grade');
$teachersCountByGrade = array_column($gradesCount, 'total');



// Préparation de la requête SQL pour obtenir le nombre moyen de salles par type de lieu
$sqlSalleType = "SELECT type_lieu, AVG(capacite) as moyenne_salles FROM lieu GROUP BY type_lieu";
try {
	// Exécution de la requête SQL
	$stmt = $myproject->query($sqlSalleType);
	// Récupération des données dans un tableau associatif
	$salleTypeData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur lors de l\'exécution de la requête : ' . $e->getMessage() . '</p>');
}
// Créer des tableaux pour stocker les types de lieux et les moyennes de salles
$typeLieu = [];
$moyenneSalles = [];

// Parcourir les données récupérées et les stocker dans les tableaux correspondants
foreach ($salleTypeData as $row) {
	$typeLieu[] = $row['type_lieu'];
	$moyenneSalles[] = $row['moyenne_salles'];
}




// Préparation de la requête SQL pour obtenir le nombre moyen de modules par spécialité
$sqlModuleBySpecialite = "SELECT activite, COUNT(*) as total_module FROM module GROUP BY activite";
try {
	// Exécution de la requête SQL
	$stmt = $myproject->query($sqlModuleBySpecialite);
	// Récupération des données dans un tableau associatif
	$moduleBySpecialiteData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur lors de l\'exécution de la requête : ' . $e->getMessage() . '</p>');
}

// Créer des tableaux pour stocker les spécialités et les nombres moyens de modules
$activite = [];
$totalModules = [];

// Parcourir les données récupérées et les stocker dans les tableaux correspondants
foreach ($moduleBySpecialiteData as $row) {
	$activite[] = $row['activite'];
	$totalModules[] = $row['total_module'];
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">


	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="assets/css/dashboard.css">


	<title>Dashboard</title>
	<style>
		.profile-menu {
			position: absolute;
			top: 60px;
			right: 10px;
			background-color: #fff;
			border: 1px solid #ccc;
			border-radius: 15px;
			box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
			display: none;
			z-index: 9999;
			padding: 10px 0;
			max-height: 0;
			overflow: hidden;
			margin-right: 5px;
			transition: max-height 0.3s ease, padding 0.3s ease;
		}

		/* @media screen and (max-width: 768px) {
			.container {
				flex-direction: column;
			}
		}
	 */
		.profile-menu ul {
			list-style: none;
			padding: 0;
			margin: 0;
		}

		.profile-menu ul li {
			padding: 10px 20px;
			display: flex;
			align-items: center;
			/* Centrer verticalement */
			margin-right: 10px;
		}

		.profile-menu ul li i {
			margin-right: 20px;
			/* Espace entre l'icône et le texte */
		}

		.profile-menu ul li:hover {
			background-color: #f0f0f0;
			cursor: pointer;
		}

		.profile-menu.active {
			display: block;
			max-height: 300px;
			padding: 10px 0;
			/* Ajustement du padding */
		}

		.container {
			display: flex;
			justify-content: space-between;
		}

		/* @media screen and (max-width: 576px) {
			.container {
				flex-direction: column;
			}
		} */
		@media screen and (max-width: 1085px) {
			.container {
				/* display: flex; */
				flex-direction: column;
			}
		}
	</style>
</head>

<body>


	<!-- SIDEBAR -->
	<section id="sidebar" class="hide">
		<a href="#" class="brand">
			<i class='bx bx-grid-alt'></i>
			<span class="text">Admin</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="dashboard.php">
					<!-- <i class='bx bxs-dashboard' ></i> -->
					<i class='bx bx-stats'></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="enseignant.php">
					<i class='bx bx-user'></i>
					<span class="text">Enseignants</span>
				</a>
			</li>
			<li>
				<a href="etudiant.php">
					<i class='bx bxs-group'></i>
					<span class="text">Etudiants</span>
				</a>
			</li>
			<li>
				<a href="salle.php">
					<i class='bx bxs-school'></i>
					<span class="text">Salles</span>
				</a>
			</li>
			<li>
				<a href="module.php">
					<i class='bx bx-file'></i>
					<span class="text">Modules</span>
				</a>
			</li>
			<li>
				<a href="notifAdmin.php">
					<i class='bx bx-message'></i>
					<span class="text">Notifications</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="planning.php">
					<i class='bx bxs-cog'></i>
					<span class="text">Generation Planning</span>
				</a>
			</li>
			<li>
				<a href="session.php?logout=true" class="logout" onclick="return logoutConfirmation()">
					<i class='bx bxs-log-out-circle'></i>
					<span class="text">Déconnexion</span>
				</a>

			</li>
		</ul>
	</section>
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu'></i>
			<a href="#" class="nav-link">Categories</a>
			<form action="#">
				<div class="form-input">
					<input type="search" placeholder="Search...">
					<button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
				</div>
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			<a href="#" class="notification">
				<i class='bx bxs-bell'></i>
				<!-- <span class="num">8</span> -->
			</a>

			<a href="#" class="profile" onclick="toggleProfileMenu()">
				<i class='bx bx-user'></i>
			</a>
			<div class="profile-menu" id="profileMenu">
				<ul>
					<li>
						<i class='bx bx-home'></i>
						<a href="dashboard.php"> Dashboard</a>
					</li>
					<li>
						<i class='bx bx-user'></i>
						<a href="profil.php"> Mon Profil</a>
					</li>
					<li>
						<i class='bx bx-edit'></i>
						<a href="profil.php"> Edit Profile</a>
					</li>
					<li>
						<i class='bx bx-log-out'></i>
						<a href="#" onclick="logoutConfirmation()"> Logout</a>
					</li>
				</ul>
			</div>


		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<section id="dash">
				<div class="head-title">
					<div class="left">
						<h1>Dashboard</h1>
						<ul class="breadcrumb">
							<li>
								<a href="#">Dashboard</a>
							</li>
							<li><i class='bx bx-chevron-right'></i></li>
							<li>
								<a class="active" href="#">Home</a>
							</li>
						</ul>
					</div>

				</div>

				<ul class="box-info">
					<li>
						<i class='bx bxs-user-detail'></i>
						<span class="text">
							<h3>
								<?php echo $total_enseignants; ?>
							</h3>
							<p>Nombre d'enseignants</p>
						</span>
					</li>
					<li>
						<i class='bx bxs-graduation'></i>
						<span class="text">
							<h3>
								<?php echo $total_module; ?>
							</h3>
							<p>Nombre de Modules</p>
						</span>
					</li>
					<li>
						<i class='bx bxs-school'></i>
						<span class="text">
							<h3>
								<?php echo $total_salles; ?>
							</h3>
							<p>Nombre de salles</p>
						</span>
					</li>
				</ul>


				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Statistiques</h3>
							<!-- <i class='bx bx-search' ></i> -->
							<i class='bx bx-filter'></i>
						</div>
						<div class="graph">
							<div class="graph-container">
								<canvas id="teachersByGradeChart"></canvas>
							</div>

							<br><br><br><br>
							<!-- Ajoutez cette balise canvas où vous voulez afficher le diagramme circulaire -->

							<div class="container">

								<div class="graph-container" style="width: 700px;">
									<canvas id="modulesBySpecialiteChart"></canvas>
								</div>
								<div class="graph-container">
									<canvas id="sallesByTypeChart"></canvas>
								</div>

							</div>
						</div>

					</div>
				</div>
			</section>



		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->



	<script>
		var ctx = document.getElementById('teachersByGradeChart').getContext('2d');
		var teachersByGradeChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: <?php echo json_encode($grades); ?>,
				datasets: [{
					label: 'Nombre d\'enseignants par grade',
					data: <?php echo json_encode($teachersCountByGrade); ?>,
					backgroundColor: '#85B8CB',
					borderColor: '#85B8CB',
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero: true,
							stepSize: 1
						}
					}]
				}
			}
		});
	</script>




	<script>
		var ctx = document.getElementById('sallesByTypeChart').getContext('2d');
		var sallesByTypeChart = new Chart(ctx, {
			type: 'pie',
			data: {
				labels: <?php echo json_encode($typeLieu); ?>,
				datasets: [{
					label: 'Nombre moyen de salles par type de lieu',
					data: <?php echo json_encode($moyenneSalles); ?>,
					backgroundColor: [
						'rgba(255, 99, 132, 0.5)',
						'rgba(54, 162, 235, 0.5)',
						'rgba(255, 206, 86, 0.5)',
						'rgba(75, 192, 192, 0.5)',
						'rgba(153, 102, 255, 0.5)',
						'rgba(255, 159, 64, 0.5)'
					],
					borderColor: [
						'rgba(255, 99, 132, 1)',
						'rgba(54, 162, 235, 1)',
						'rgba(255, 206, 86, 1)',
						'rgba(75, 192, 192, 1)',
						'rgba(153, 102, 255, 1)',
						'rgba(255, 159, 64, 1)'
					],
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false, // Désactiver l'ajustement automatique de l'aspect ratio
				legend: {
					position: 'top',
				},
				title: {
					display: true,
					text: 'Nombre moyen de salles par type de lieu'
				},
				animation: {
					animateScale: true,
					animateRotate: true
				}
			}
		});
	</script>







	<script>
		var ctx = document.getElementById('modulesBySpecialiteChart').getContext('2d');
		var modulesBySpecialiteChart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: <?php echo json_encode($activite); ?>,
				datasets: [{
					label: 'Nombre moyen de modules par activités',
					data: <?php echo json_encode($totalModules); ?>,
					backgroundColor: 'rgba(54, 162, 235, 0.2)',
					borderColor: 'rgba(54, 162, 235, 1)',
					borderWidth: 1
				}]
			},
			options: {
				scales: {
					y: {
						beginAtZero: true
					}
				}
			}
		});
	</script>




	<script>
		function toggleProfileMenu() {
			var profileMenu = document.getElementById('profileMenu');
			profileMenu.classList.toggle('active');
		}

		function logoutConfirmation() {
			if (confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
				return true; // Si l'utilisateur confirme, la déconnexion se produit
			} else {
				return false; // Si l'utilisateur annule, la déconnexion est annulée
			}
		}
	</script>

	<script src="assets/js/dashboard.js"></script>
	<script src="https://unpkg.com/@popperjs/core@2"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>x
</body>

</html>