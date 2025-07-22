-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 11 juil. 2025 à 14:09
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `laravelq`
--

-- --------------------------------------------------------

--
-- Structure de la table `business_cards`
--

CREATE TABLE `business_cards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `title` varchar(191) DEFAULT NULL,
  `company` varchar(191) DEFAULT NULL,
  `domain` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `website` varchar(191) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `social_media` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_media`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `business_cards`
--

INSERT INTO `business_cards` (`id`, `user_id`, `image`, `name`, `position`, `industry`, `title`, `company`, `domain`, `phone`, `email`, `website`, `address`, `logo`, `created_at`, `updated_at`, `bio`, `social_media`) VALUES
(1, NULL, NULL, 'ABEL', 'sd', NULL, NULL, 'Formation', NULL, '23322323', 'fokamfotso4@gmail.com', 'http://127.0.0.1:8000/business-card', 'pk14', 'business-cards/logos/bldb0QPzTJ0gBSD4Q0iEgsEPH6KfOUYlEqKTohdN.jpg', '2025-06-17 13:42:05', '2025-06-17 13:42:05', NULL, NULL),
(2, 1, NULL, 'admin', 'as', 'Finance', NULL, 'Formation', NULL, '2456656', 'admin@gmail.com', 'http://127.0.0.1:8000/business-card', 'pk14', 'logos/jMSqMQtLbz57FAXH6eDq0JHgRsTlnxWwrHhyfJKV.jpg', '2025-06-17 17:29:27', '2025-06-17 17:29:27', 'dsgsg', '\"{\\\"linkedin\\\":\\\"http:\\\\\\/\\\\\\/127.0.0.1:8000\\\\\\/business-card\\\",\\\"twitter\\\":\\\"http:\\\\\\/\\\\\\/127.0.0.1:8000\\\\\\/business-card\\\"}\"'),
(3, 2, NULL, 'dddd', 'sd', 'Marketing', NULL, 'ffg', NULL, '1234567', 'iam.asm89@gmail.com', 'http://127.0.0.1:8000/business-card', 'pk14', 'business-cards/logos/hPGYturp0N0ZUobuFr20iYcq0a03gTnvY2S7DJQn.jpg', '2025-06-20 08:05:04', '2025-06-20 08:05:04', 'dfafa', '\"{\\\"linkedin\\\":\\\"http:\\\\\\/\\\\\\/127.0.0.1:8000\\\\\\/business-card\\\",\\\"twitter\\\":\\\"http:\\\\\\/\\\\\\/127.0.0.1:8000\\\\\\/business-card\\\"}\"'),
(4, 3, NULL, 'ABEL', 'as', 'Éducation', NULL, 'wqwq', NULL, '1234567', 'iam.asm89@gmail.co', 'http://127.0.0.1:8000/business-card', 'pk14', 'business-cards/logos/YPHMc0aLOZiIjyISeqeeo9L1mOMrtxYxQHIPlDZh.jpg', '2025-07-05 12:34:59', '2025-07-05 12:34:59', 'Swift_TransportException\r\nConnection could not be established with host mailhog :stream_socket_client(): php_network_getaddresses: getaddrinfo failed: H�te inconnu.', '\"{\\\"linkedin\\\":\\\"http:\\\\\\/\\\\\\/127.0.0.1:8000\\\\\\/business-card\\\",\\\"twitter\\\":\\\"http:\\\\\\/\\\\\\/127.0.0.1:8000\\\\\\/business-card\\\"}\"'),
(5, 4, NULL, 'asdasf', 'sd', 'Commerce', NULL, 'afafaf', NULL, '23322323', 'iam.asm89@gmail.co', 'http://127.0.0.1:8000/business-card', 'pk14', 'business-cards/logos/RNZbn7JLTQrXloJAscF4A8vXGztUFy9r5BuyEYzq.jpg', '2025-07-08 01:53:35', '2025-07-08 01:53:35', 'zvzv', '\"{\\\"linkedin\\\":\\\"http:\\\\\\/\\\\\\/127.0.0.1:8000\\\\\\/business-card\\\",\\\"twitter\\\":\\\"http:\\\\\\/\\\\\\/127.0.0.1:8000\\\\\\/business-card\\\"}\"');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `color`, `image`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'dddd', 'dddd', 'wewe', 'asa', '#d33c3c', 'categories/1vH19MiIYrHdTVhjUnNoeyGsb8AC26p0qboEWhic.jpg', 1, '2025-06-17 12:49:11', '2025-06-17 12:49:11'),
(2, 'ABEL', 'abel', 'qwertyuioplkjhgfdsa', 'asa', '#4b9be2', 'categories/TPztyfIWSYB2NPJNjD7N747ru5LpIITg4D7MFvQy.jpg', 1, '2025-07-06 01:39:51', '2025-07-06 01:39:51'),
(3, 'wert', 'wert', 'dfsdffsd', 'asa', '#7ec723', 'categories/FYzzOYoZuye0oQEWhwBlqqXUj7cQQbBn0EKZGtol.jpg', 1, '2025-07-08 01:56:12', '2025-07-08 01:56:12');

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `content`, `event_id`, `user_id`, `parent_id`, `approved`, `created_at`, `updated_at`, `status`) VALUES
(1, 'is good', 1, 3, NULL, 1, '2025-07-05 19:25:29', '2025-07-05 19:25:29', 'pending'),
(2, 'cest super', 1, 1, 1, 1, '2025-07-05 20:10:43', '2025-07-05 20:10:43', 'pending'),
(3, 'wety', 2, 1, NULL, 1, '2025-07-06 01:42:32', '2025-07-06 01:42:32', 'pending'),
(4, 'cep', 2, 1, NULL, 1, '2025-07-08 01:57:22', '2025-07-08 01:57:22', 'pending'),
(5, 'send', 1, 1, NULL, 1, '2025-07-11 10:02:55', '2025-07-11 10:02:55', 'pending');

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `event_date` timestamp NULL DEFAULT NULL,
  `location` varchar(191) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(191) NOT NULL DEFAULT 'draft',
  `logo_path` varchar(191) DEFAULT NULL,
  `background_image_path` varchar(191) DEFAULT NULL,
  `theme_color` varchar(7) DEFAULT NULL,
  `custom_css` text DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `title`, `slug`, `content`, `excerpt`, `category_id`, `user_id`, `published_at`, `event_date`, `location`, `views`, `featured`, `status`, `logo_path`, `background_image_path`, `theme_color`, `custom_css`, `image`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'sdf', 'sdf', 'xcxc', 'xc', 1, 3, '2025-07-09 12:22:00', '2025-07-07 11:19:00', NULL, 42, 0, 'published', NULL, NULL, NULL, NULL, NULL, '2025-07-05 19:20:05', '2025-07-11 10:02:59', NULL),
(2, 'qqert', 'qqert', 'asdafgdf', 'qrcgccdfgfhd', 2, 1, NULL, '2025-07-15 07:43:00', 'daad', 11, 1, 'published', NULL, NULL, NULL, NULL, 'events/poxtmVmPe2KA8HjdUt0QlkrkK8OgTCCG21Ru5b7b.jpg', '2025-07-06 01:40:38', '2025-07-11 10:08:56', NULL),
(3, 'eoicxn jxc', 'eoicxn-jxc', 'afsadfgsdgdg', 'sgsgsgsg', 3, 1, NULL, '2025-07-09 06:59:00', 'zxzxzxc', 3, 0, 'published', NULL, NULL, NULL, NULL, 'events/LudBCpWPGB3gbdFfkfDFYDg5CLA532ArTVjXaNqh.jpg', '2025-07-08 01:56:54', '2025-07-11 08:13:41', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `event_tag`
--

CREATE TABLE `event_tag` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `event_user`
--

CREATE TABLE `event_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected','blocked') NOT NULL DEFAULT 'pending',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `invitations`
--

CREATE TABLE `invitations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `status` enum('pending','sent','accepted','rejected') NOT NULL DEFAULT 'pending',
  `message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

CREATE TABLE `likes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `likeable_id` bigint(20) DEFAULT NULL,
  `likeable_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `event_id`, `created_at`, `updated_at`, `likeable_id`, `likeable_type`) VALUES
(1, 3, 1, '2025-07-05 19:25:43', '2025-07-05 19:25:43', 0, ''),
(8, 1, 2, '2025-07-11 10:05:11', '2025-07-11 10:05:11', 123, 'App\\Models\\Event');

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `file_type` varchar(191) NOT NULL,
  `mime_type` varchar(191) NOT NULL,
  `size` int(11) DEFAULT NULL,
  `event_id` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `media`
--

INSERT INTO `media` (`id`, `name`, `file_path`, `file_type`, `mime_type`, `size`, `event_id`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 'IMG-20250701-WA0002.jpg', 'events/1/1751750405_IMG-20250701-WA0002.jpg', 'image', 'image/jpeg', 80079, 1, 3, '2025-07-05 19:20:05', '2025-07-05 19:20:05'),
(2, 'IMG-20250702-WA0013.jpg', 'events/1/1751751765_IMG-20250702-WA0013.jpg', 'image', 'image/jpeg', 91502, 1, 3, '2025-07-05 19:42:45', '2025-07-05 19:42:45');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `content`, `created_at`, `updated_at`, `read_at`) VALUES
(1, 1, 2, 'ssd', '2025-06-20 09:22:32', '2025-06-20 09:25:40', '2025-06-20 09:25:40'),
(2, 1, 2, 'ssd', '2025-06-20 09:24:49', '2025-06-20 09:25:40', '2025-06-20 09:25:40'),
(3, 1, 2, 'ssd', '2025-06-20 09:24:56', '2025-06-20 09:25:40', '2025-06-20 09:25:40'),
(4, 2, 1, 'ssd', '2025-06-20 09:25:44', '2025-06-20 11:01:18', '2025-06-20 11:01:18'),
(5, 2, 1, 'ssd', '2025-06-20 09:43:57', '2025-06-20 11:01:18', '2025-06-20 11:01:18'),
(6, 2, 1, 'ssd', '2025-06-20 11:00:33', '2025-06-20 11:01:18', '2025-06-20 11:01:18'),
(7, 2, 1, 'ssd', '2025-06-20 11:00:47', '2025-06-20 11:01:18', '2025-06-20 11:01:18'),
(8, 1, 2, 'abv', '2025-06-20 11:03:43', '2025-06-20 11:14:42', '2025-06-20 11:14:42'),
(9, 1, 2, 'abel', '2025-06-20 11:14:21', '2025-06-20 11:14:42', '2025-06-20 11:14:42'),
(10, 2, 1, 'sdkw', '2025-06-20 12:04:51', '2025-06-20 12:05:15', '2025-06-20 12:05:15'),
(11, 2, 1, 'salut Elia', '2025-06-20 12:53:02', '2025-06-20 12:53:57', '2025-06-20 12:53:57'),
(12, 1, 2, 'yes dogmo', '2025-06-20 12:54:23', '2025-07-05 20:30:08', '2025-07-05 20:30:08'),
(13, 3, 2, 'salut Elia', '2025-07-05 19:09:27', '2025-07-05 19:09:27', NULL),
(14, 3, 2, 'yes dogmo', '2025-07-05 20:09:16', '2025-07-05 20:09:16', NULL),
(15, 2, 1, 'abv', '2025-07-05 20:30:15', '2025-07-05 20:40:00', '2025-07-05 20:40:00'),
(16, 1, 2, 'ddhdhd', '2025-07-06 01:41:27', '2025-07-06 01:41:27', NULL),
(17, 4, 1, 'salut', '2025-07-08 01:54:11', '2025-07-08 01:55:25', '2025-07-08 01:55:25'),
(18, 4, 1, 'salut', '2025-07-08 01:54:15', '2025-07-08 01:55:25', '2025-07-08 01:55:25'),
(19, 4, 1, 'salut', '2025-07-08 01:54:16', '2025-07-08 01:55:25', '2025-07-08 01:55:25'),
(20, 1, 4, 'oui', '2025-07-08 01:55:30', '2025-07-08 01:55:30', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_04_30_160000_create_tags_table', 1),
(6, '2025_04_30_160145_create_categories_table', 1),
(7, '2025_04_30_160157_create_events_table', 1),
(8, '2025_04_30_160208_create_comments_table', 1),
(9, '2025_04_30_160220_create_media_table', 1),
(10, '2025_04_30_160229_create_notifications_table', 1),
(11, '2025_04_30_161004_create_event_tag_table', 1),
(12, '2025_04_30_161102_create_likes_table', 1),
(13, '2025_05_03_040230_create_business_cards_table', 1),
(14, '2025_05_03_040230_create_notification_preferences_table', 1),
(15, '2024_03_19_add_customization_fields_to_events_table', 2),
(16, '2024_03_19_add_soft_deletes_to_events_table', 2),
(17, '2024_03_19_create_event_user_table', 2),
(18, '2024_03_19_create_invitations_table', 2),
(19, '2024_01_01_000000_add_user_id_to_business_cards_table', 3),
(20, '2025_07_05_224345_add_sender_id_to_notifications_table', 4);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `type`, `user_id`, `sender_id`, `event_id`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 'Nouveau message de admin', 'ssd', 'message', 2, NULL, NULL, 1, '2025-06-20 09:22:32', '2025-06-20 09:25:40'),
(2, 'Nouveau message de admin', 'ssd', 'message', 2, NULL, NULL, 1, '2025-06-20 09:24:49', '2025-06-20 09:25:40'),
(3, 'Nouveau message de admin', 'ssd', 'message', 2, NULL, NULL, 1, '2025-06-20 09:24:56', '2025-06-20 09:25:40'),
(8, 'Nouveau message de admin', 'abv', 'message', 2, NULL, NULL, 1, '2025-06-20 11:03:43', '2025-06-20 11:14:42'),
(9, 'Nouveau message de admin', 'abel', 'message', 2, NULL, NULL, 1, '2025-06-20 11:14:21', '2025-06-20 11:14:42'),
(11, 'Nouveau message de ABEL', 'salut Elia', 'message', 1, NULL, NULL, 1, '2025-06-20 12:53:02', '2025-07-08 01:55:25'),
(12, 'Nouveau message de admin', 'yes dogmo', 'message', 2, NULL, NULL, 1, '2025-06-20 12:54:23', '2025-07-05 20:30:08'),
(13, 'Nouveau message de ABEL', 'salut Elia', 'message', 2, NULL, NULL, 1, '2025-07-05 19:09:27', '2025-07-05 20:28:45'),
(14, 'Nouveau message de ABEL', 'yes dogmo', 'message', 2, NULL, NULL, 1, '2025-07-05 20:09:16', '2025-07-05 20:30:08'),
(15, 'Nouveau commentaire', 'admin a commenté votre événement \"sdf\"', 'comment', 3, NULL, 1, 0, '2025-07-05 20:10:43', '2025-07-05 20:10:43'),
(16, 'Nouveau message de ABEL', 'abv', 'message', 1, NULL, NULL, 1, '2025-07-05 20:30:15', '2025-07-05 20:40:00'),
(17, 'Nouveau message de admin', 'ddhdhd', 'message', 2, NULL, NULL, 0, '2025-07-06 01:41:27', '2025-07-06 01:41:27'),
(18, 'Nouveau message de conference', 'salut', 'message', 1, NULL, NULL, 1, '2025-07-08 01:54:11', '2025-07-08 01:55:25'),
(19, 'Nouveau message de conference', 'salut', 'message', 1, NULL, NULL, 1, '2025-07-08 01:54:15', '2025-07-08 01:55:25'),
(20, 'Nouveau message de conference', 'salut', 'message', 1, NULL, NULL, 1, '2025-07-08 01:54:16', '2025-07-08 01:55:25'),
(21, 'Nouveau message de admin', 'oui', 'message', 4, NULL, NULL, 0, '2025-07-08 01:55:30', '2025-07-08 01:55:30'),
(22, 'Nouveau commentaire', 'admin a commenté votre événement \"sdf\"', 'comment', 3, NULL, 1, 0, '2025-07-11 10:02:55', '2025-07-11 10:02:55');

-- --------------------------------------------------------

--
-- Structure de la table `notification_preferences`
--

CREATE TABLE `notification_preferences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`categories`)),
  `types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`types`)),
  `email_notifications` tinyint(1) NOT NULL DEFAULT 1,
  `push_notifications` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('fokamfotso4@gmail.com', '$2y$10$DWIgqvvMYhJzTHTGHH/O2eXpxXgf5LG2tci0vaCFwsGJ28edKKSDS', '2025-07-05 12:28:53');

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

CREATE TABLE `tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role` enum('admin','editor','user') NOT NULL DEFAULT 'user',
  `notification_preferences` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `notification_preferences`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', NULL, '$2y$10$tVL4.wTRFPkBVyAs71t/9uXlBChgZVWjQgX5mH0ZC3sxYcvjAPjii', 'admin', 1, NULL, '2025-06-07 16:58:59', '2025-06-07 16:58:59'),
(2, 'ABEL', 'fokamfotso4@gmail.com', NULL, '$2y$10$TY0q9fpfxr0fF321/9mXz.hGUV1hsLSjAz1/CNXpLs8PqAtSPXilu', 'user', 1, NULL, '2025-06-17 13:12:06', '2025-06-17 13:12:06'),
(3, 'ABEL', 'fokamfotso@gmail.com', NULL, '$2y$10$WSz/PYGx/5LAVSaPKydksOUzRVklASWH71N7rfwk2QGxbOz90uKkC', 'user', 1, NULL, '2025-07-05 12:33:59', '2025-07-05 12:33:59'),
(4, 'conference', 'iam.asm89@gmail.co', NULL, '$2y$10$/8wKSOV8XX50AAMhHjW84eM76AidqdZ5bsmvnHOUDEoy8itbTrBfW', 'user', 1, NULL, '2025-07-08 01:52:51', '2025-07-08 01:52:51');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `business_cards`
--
ALTER TABLE `business_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_cards_user_id_foreign` (`user_id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_created_by_foreign` (`created_by`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_event_id_foreign` (`event_id`),
  ADD KEY `comments_user_id_foreign` (`user_id`),
  ADD KEY `comments_parent_id_foreign` (`parent_id`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `events_slug_unique` (`slug`),
  ADD KEY `events_category_id_foreign` (`category_id`),
  ADD KEY `events_user_id_foreign` (`user_id`);

--
-- Index pour la table `event_tag`
--
ALTER TABLE `event_tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_tag_event_id_foreign` (`event_id`),
  ADD KEY `event_tag_tag_id_foreign` (`tag_id`);

--
-- Index pour la table `event_user`
--
ALTER TABLE `event_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_user_event_id_user_id_unique` (`event_id`,`user_id`),
  ADD KEY `event_user_user_id_foreign` (`user_id`),
  ADD KEY `event_user_status_index` (`status`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invitations_token_unique` (`token`),
  ADD KEY `invitations_event_id_email_index` (`event_id`,`email`),
  ADD KEY `invitations_status_index` (`status`);

--
-- Index pour la table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_event_id_foreign` (`event_id`),
  ADD KEY `media_uploaded_by_foreign` (`uploaded_by`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_event_id_foreign` (`event_id`),
  ADD KEY `notifications_sender_id_foreign` (`sender_id`);

--
-- Index pour la table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_preferences_user_id_foreign` (`user_id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tags_slug_unique` (`slug`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `business_cards`
--
ALTER TABLE `business_cards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `event_tag`
--
ALTER TABLE `event_tag`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `event_user`
--
ALTER TABLE `event_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `business_cards`
--
ALTER TABLE `business_cards`
  ADD CONSTRAINT `business_cards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `event_tag`
--
ALTER TABLE `event_tag`
  ADD CONSTRAINT `event_tag_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `event_user`
--
ALTER TABLE `event_user`
  ADD CONSTRAINT `event_user_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `invitations_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `media_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notification_preferences`
--
ALTER TABLE `notification_preferences`
  ADD CONSTRAINT `notification_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
