<?php
require 'session.php';
include 'connection.php';
$error_message = '';

try {
	$myproject = new PDO("mysql:host=localhost;dbname=myproject; charset=utf8;", "root", "");
	$myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$error_message = '<p class="text-danger">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>';
}

if (isset($_SESSION['name'])) {
	$name = $_SESSION['name'];

	// Vérification si le formulaire a été soumis
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
		$new_code = $_POST['edit-code'];
		$new_password = $_POST['edit-mdp'];

		// Mise à jour des informations de l'administrateur
		$update_sql = "UPDATE admin SET name = ?, password = ? WHERE name = ?";
		$update_stmt = $myproject->prepare($update_sql);
		$update_stmt->execute([$new_code, $new_password, $name]);

		// Mise à jour de la session avec le nouveau nom d'utilisateur
		$_SESSION['name'] = $new_code;
		$name = $new_code;

		//	echo '<p class="text-success">Profil mis à jour avec succès.</p>';
	}

	// Requête SQL pour sélectionner les informations de l'administrateur
	$sql = "SELECT * FROM admin WHERE name = ?";
	$stmt = $myproject->prepare($sql);
	$stmt->execute([$name]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$row) {
		echo "<p>Aucune information trouvée pour cet administrateur.</p>";
	} else {
?>
		<!DOCTYPE html>
		<html lang="en">

		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
			<link rel="stylesheet" href="assets/css/profil.css">
			<title>Profil</title>
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
			<section id="sidebar" class="hide">
				<a href="#" class="brand">
					<i class='bx bx-grid-alt'></i>
					<span class="text">Admin</span>
				</a>
				<ul class="side-menu top">
					<li>
						<a href="dashboard.php">
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
							<span class="text">Modules</span>
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

			<section id="content">
				<nav>
					<i class='bx bx-menu'></i>
					<form action="#">
					</form>
					<input type="checkbox" id="switch-mode" hidden>
					<label for="switch-mode" class="switch-mode"></label>
					<a href="#" class="notification">
						<i class='bx bxs-bell'></i>
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

				<main>
					<section id="profil">
						<div class="head-title">
							<div class="left">
								<ul class="breadcrumb">
									<li>
										<a href="#">Dashboard</a>
									</li>
									<li><i class='bx bx-chevron-right'></i></li>
									<li>
										<a class="active" href="#">Profil</a>
									</li>
								</ul>
							</div>
						</div>
					</section>

					<div class="container">
						<h1>Profil Administrateur</h1>
						<div class="profile-info">
							<h2>Informations</h2>
							<p><strong>Code :</strong> <?php echo htmlspecialchars($name); ?></p>
							<p><strong>Mot de passe :</strong> <?php echo htmlspecialchars($row['password']); ?></p>
						</div>

						<h1>Éditer le profil</h1>
						<div class="profile-edit">
							<form action="" method="post">
								<label for="edit-code">Code:</label>
								<input type="text" id="edit-code" name="edit-code" value="<?php echo htmlspecialchars($row['name']); ?>">
								<label for="edit-mdp">Mot de passe:</label>
								<input type="text" id="edit-mdp" name="edit-mdp" value="<?php echo htmlspecialchars($row['password']); ?>">
								<button type="submit" name="edit_profile">Enregistrer</button>
							</form>
						</div>

					</div>
				</main>

				<script>
					function toggleProfileMenu() {
						var profileMenu = document.getElementById('profileMenu');
						profileMenu.classList.toggle('active');
					}

					function logoutConfirmation() {
						return confirm("Êtes-vous sûr de vouloir vous déconnecter ?");
					}


					const switchMode = document.getElementById('switch-mode');

					switchMode.addEventListener('change', function() {
						if (this.checked) {
							document.body.classList.add('dark');
						} else {
							document.body.classList.remove('dark');
						}
					})
				</script>

				<script src="assets/js/profil.js"></script>
		</body>

		</html>
<?php
	}
} else {
	echo "<p>Vous n'êtes pas connecté.</p>";
}
?>