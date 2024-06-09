<?php
try {
	$myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
	$myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//echo ('<p style="color: green;">Connexion à la base de données réussie.</p>');
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>');
}

// Préparation de la requête SQL
$sqlModule = "SELECT * FROM module";

try {
	// Exécution de la requête SQL
	$stmt = $myproject->query($sqlModule);
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur lors de l\'exécution de la requête : ' . $e->getMessage() . '</p>');
}

$sqlSpecialite = "SELECT * FROM specialite";

try {
	// Exécution de la requête SQL
	$stmtSpecialite = $myproject->query($sqlSpecialite);
} catch (PDOException $e) {
	die('<p style="color: red;">Erreur lors de l\'exécution de la requête : ' . $e->getMessage() . '</p>');
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="assets/css/module.css">
	<title>Modules</title>

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

		.btn-create {
			display: inline-block;
			background-color: #4CAF50;
			color: white;
			padding: 10px 20px;
			text-align: center;
			text-decoration: none;
			font-size: 16px;
			border-radius: 5px;
			transition: background-color 0.3s;
		}

		.btn-create:hover {
			background-color: #45a049;
		}

		.btn-create i {
			margin-right: 5px;
		}

		.btn-edit {
			display: inline-block;
			background-color: #007bff;
			color: white;
			padding: 5px 10px;
			text-align: center;
			text-decoration: none;
			font-size: 14px;
			border-radius: 3px;
			transition: background-color 0.3s;
		}

		.btn-edit:hover {
			background-color: #0056b3;
		}

		.btn-edit i {
			margin-right: 5px;
		}

		.btn-delete {
			display: inline-block;
			background-color: #dc3545;
			color: white;
			padding: 5px 10px;
			text-align: center;
			text-decoration: none;
			font-size: 14px;
			border: none;
			/* Ajoutez cette ligne pour supprimer les bordures */
			border-radius: 3px;
			transition: background-color 0.3s;
		}

		.btn-delete:hover {
			background-color: #c82333;
		}

		.btn-delete i {
			margin-right: 5px;
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
			<li>
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
			<li class="active">
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

	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu'></i>
			<a href="#" class="nav-link">Categories</a>
			<form action="#">
				<div class="form-input">
					<input type="search" name="search" id="searchInput" placeholder="Search...">
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
						<h1>Liste des Modules</h1>
						<ul class="breadcrumb">
							<li>
								<a href="#">Dashboard</a>
							</li>
							<li><i class='bx bx-chevron-right'></i></li>
							<li>
								<a class="active" href="#">Modules</a>
							</li>
						</ul>
					</div>
				</div>




				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Spécialités</h3>
							<!-- <i class='bx bx-search' ></i> -->
							<i class='bx bx-filter'></i>
							<div class="text-center">
								<a href="createSpecialite.php" class="btn-create"><i class='bx bx-plus'></i> Créer
									Spécialité</a>
							</div>
						</div>
						<table>
							<thead>
								<tr>
									<th>Nom</th>
									<th>Faculté</th>
									<th>Modifier</th>
									<th>Suprimer</th>
								</tr>
							</thead>

							<tbody id="specialiteTable">
								<?php
								include('connection.php');
								// Affichage des résultats dans le tableau
								while ($row = $stmtSpecialite->fetch(PDO::FETCH_ASSOC)) {
									echo "<tr>";
									echo "<td>" . htmlspecialchars($row['nom_specialite']) . "</td>";
									echo "<td>" . htmlspecialchars($row['faculte']) . "</td>";
									echo "<td><a href='modifierSpecialite.php?nom_specialite=" . $row['nom_specialite'] . "' class='btn-edit'><i class='bx bx-edit'></i> Modifier</a></td>";
									echo "<td><button onclick=\"deleteSpecialite('" . htmlspecialchars($row['nom_specialite']) . "')\" class='btn-delete'><i class='bx bx-trash'></i> Supprimer</button></td>";
									echo "</tr>";
								}
								?>
							</tbody>

						</table>
					</div>
				</div>





				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Modules</h3>
							<!-- <i class='bx bx-search' ></i> -->
							<i class='bx bx-filter'></i>
							<div class="text-center">
								<a href="createModule.php" class="btn-create"><i class='bx bx-plus'></i> Créer
									Module</a>
							</div>
						</div>
						<table>
							<thead>
								<tr>
									<th>Identifiant</th>
									<th>Intitulé</th>
									<th>Spécialité</th>
									<th>Le chargé de Module</th>
									<th>Activités</th>
									<th>Modifier</th>
									<th>Suprimer</th>
								</tr>
							</thead>

							<tbody id="moduleTable">
								<?php
								include('connection.php');
								// Affichage des résultats dans le tableau
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									echo "<tr>";
									echo "<td>" . htmlspecialchars($row['id_module']) . "</td>";
									echo "<td>" . htmlspecialchars($row['nom_module']) . "</td>";
									echo "<td>" . htmlspecialchars($row['nom_specialite']) . "</td>";
									echo "<td>" . htmlspecialchars($row['charge_module']) . "</td>";
									echo "<td>" . htmlspecialchars($row['activite']) . "</td>";



									echo "<td><a href='modifierModule.php?id_module=" . $row['id_module'] . "' class='btn-edit'><i class='bx bx-edit'></i> Modifier</a></td>";
									echo "<td><button onclick='deleteModule(" . $row['id_module'] . ")' class='btn-delete'><i class='bx bx-trash'></i> Supprimer</button></td>";
									echo "</tr>";
								}
								?>
							</tbody>

						</table>
					</div>
				</div>

				<script>
					function deleteSpecialite(nom_specialite) {
						if (confirm("Êtes-vous sûr de vouloir supprimer cette Specialite ?")) {
							window.location.href = "supprimerSpecialite.php?nom_specialite=" + nom_specialite;
						}
					}
				</script>

				<script>
					function deleteModule(id_module) {
						if (confirm("Êtes-vous sûr de vouloir supprimer ce Module ?")) {
							window.location.href = "supprimerModule.php?id_module=" + id_module;
						}
					}
				</script>


			</section>

			<script src="assets/js/module.js"></script>
		</main>
	</section>
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



</body>

</html>