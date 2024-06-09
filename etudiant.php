<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="assets/css/etudiant.css">
	<title>Groupes</title>
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
			<li class="active">
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
						<h1>Liste des Groupes</h1>
						<ul class="breadcrumb">
							<li>
								<a href="#">Dashboard</a>
							</li>
							<li><i class='bx bx-chevron-right'></i></li>
							<li>
								<a class="active" href="#">Groupes</a>
							</li>
						</ul>
					</div>
				</div>



				<div class="table-data">
					<div class="order">
						<div class="head">
							<h3>Groupes</h3>
							<!-- <i class='bx bx-search' ></i> -->
							<i class='bx bx-filter'></i>
							<div class="text-center">
								<a href="createGroup.php" class="btn-create"><i class='bx bx-plus'></i> Créer
									Groupe</a>
							</div>
						</div>
						<table>
							<thead>
								<tr>
									<th>Spécialité</th>
									<th>Section</th>
									<th>Nom Groupe</th>


									<th>Nombre d'étudiants</th>
									<th>Modifier</th>
									<th>Suprimer</th>

								</tr>
							</thead>
							<tbody id="groupTable">
								<?php
								try {
									$myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
									$myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
									//echo ('<p style="color: green;">Connexion à la base de données réussie.</p>');
								} catch (PDOException $e) {
									die('<p style="color: red;">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>');
								}

								// Requête SQL pour récupérer les groupes, leurs spécialités associées et le nombre d'étudiants
								$sql = "SELECT g.nom_groupe, g.section, s.nom_specialite, g.nombre_etudiants
                                        FROM groupe g INNER JOIN specialite s ON g.nom_specialite = s.nom_specialite";

								// Préparation de la requête
								$stmt = $myproject->prepare($sql);
								$stmt->execute();

								// Affichage des résultats dans le tableau
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									echo "<tr>";
									echo "<td>" . $row['nom_specialite'] . "</td>";
									echo "<td>" . $row['section'] . "</td>";
									echo "<td>" . $row['nom_groupe'] . "</td>";

									echo "<td>" . $row['nombre_etudiants'] . "</td>";
									echo "<td><a href='modifierGroupe.php?nom_groupe=" . urlencode($row['nom_groupe']) . "&section=" . urlencode($row['section']) . "&nom_specialite=" . urlencode($row['nom_specialite']) . "' class='btn-edit'><i class='bx bx-edit'></i> Modifier</a></td>";
									echo "<td><button onclick=\"deleteGroup('" . htmlspecialchars($row['nom_groupe']) . "', '" . htmlspecialchars($row['section']) . "', '" . htmlspecialchars($row['nom_specialite']) . "')\" class='btn-delete'><i class='bx bx-trash'></i> Supprimer</button></td>";
									echo "</tr>";
								}
								?>
							</tbody>


						</table>
					</div>
				</div>
				<script>
					function deleteGroup(nom_groupe, section, nom_specialite) {
						if (confirm("Êtes-vous sûr de vouloir supprimer ce groupe ?")) {
							// Ajoutez la section et le nom de spécialité à l'URL de redirection
							window.location.href = "supprimerGroupe.php?nom_groupe=" + nom_groupe + "&section=" + section + "&nom_specialite=" + nom_specialite;
						}
					}
				</script>

			</section>

			<script src="assets/js/etudiant.js"></script>
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