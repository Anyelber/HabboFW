-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-10-2022 a las 08:12:38
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `habbo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `amigos`
--

CREATE TABLE `amigos` (
  `id` int(11) NOT NULL,
  `id_user1` int(11) NOT NULL,
  `id_user2` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ascensos`
--

CREATE TABLE `ascensos` (
  `id` int(11) NOT NULL,
  `id_dio` int(11) NOT NULL,
  `id_recibe` int(11) NOT NULL,
  `old_rol` int(11) NOT NULL,
  `new_rol` int(11) NOT NULL,
  `mision` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `pagado` int(11) NOT NULL,
  `pagado_recibe` int(11) NOT NULL,
  `tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ascensos_admin`
--

CREATE TABLE `ascensos_admin` (
  `id` int(11) NOT NULL,
  `id_dio` int(11) NOT NULL,
  `id_recibe` int(11) NOT NULL,
  `old_rol` int(11) NOT NULL,
  `new_rol` int(11) NOT NULL,
  `mision` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `pagado` int(11) NOT NULL,
  `tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `atencion`
--

CREATE TABLE `atencion` (
  `id` int(11) NOT NULL,
  `id_dio` int(11) NOT NULL,
  `id_recibe` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `texto` text COLLATE utf8_unicode_ci NOT NULL,
  `tipo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `status` int(11) NOT NULL,
  `fecha_visto` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configs`
--

CREATE TABLE `configs` (
  `id` int(11) NOT NULL,
  `tipo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `valor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `configs`
--

INSERT INTO `configs` (`id`, `tipo`, `valor`) VALUES
(1, 'work', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `degrados`
--

CREATE TABLE `degrados` (
  `id` int(11) NOT NULL,
  `id_dio` int(11) NOT NULL,
  `id_recibe` int(11) NOT NULL,
  `old_rol` int(11) NOT NULL,
  `new_rol` int(11) NOT NULL,
  `mision` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `tipo` int(11) NOT NULL,
  `motivo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_atencion`
--

CREATE TABLE `imagenes_atencion` (
  `id` int(11) NOT NULL,
  `id_atencion` int(11) NOT NULL,
  `imagen` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `id_noticia` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `fecha` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `texto` text COLLATE utf8_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `imagen` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pinned` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_ganador` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `texto` text COLLATE utf8_unicode_ci NOT NULL,
  `id_recibe` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagas`
--

CREATE TABLE `pagas` (
  `id` int(11) NOT NULL,
  `id_dio` int(11) NOT NULL,
  `id_recibe` int(11) NOT NULL,
  `ascensos` int(11) NOT NULL,
  `times` int(11) NOT NULL,
  `horas` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `rol_pago` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peticiones`
--

CREATE TABLE `peticiones` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `peticion` text COLLATE utf8_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `deleted` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `precio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `precio`) VALUES
(1, 'Oficinista', -1),
(2, 'Agente C', -1),
(3, 'Agente B', -1),
(4, 'Agente A', -1),
(5, 'Seguridad G', -1),
(6, 'Seguridad F', -1),
(7, 'Seguridad E', -1),
(8, 'Seguridad D', -1),
(9, 'Seguridad C', -1),
(10, 'Seguridad B', -1),
(11, 'Seguridad A', -1),
(12, 'Tecnico G', -1),
(13, 'Tecnico F', -1),
(14, 'Tecnico E', -1),
(15, 'Tecnico D', -1),
(16, 'Tecnico C', -1),
(17, 'Tecnico B', -1),
(18, 'Tecnico A', -1),
(19, 'Logistica G', 20),
(20, 'Logistica F', 20),
(21, 'Logistica E', 20),
(22, 'Logistica D', 20),
(23, 'Logistica C', 20),
(24, 'Logistica B', 20),
(25, 'Logistica A', 20),
(26, 'Supervisor G', 30),
(27, 'Supervisor F', 40),
(28, 'Supervisor E', 50),
(29, 'Supervisor D', 60),
(30, 'Supervisor C', 70),
(31, 'Supervisor B', 80),
(32, 'Supervisor A', 90),
(33, 'Director G', 100),
(34, 'Director F', 120),
(35, 'Director E', 140),
(36, 'Director D', 160),
(37, 'Director C', 180),
(38, 'Director B', 200),
(39, 'Director A', 220),
(40, 'Presidente G', 260),
(41, 'Presidente F', 300),
(42, 'Presidente E', 340),
(43, 'Presidente D', 380),
(44, 'Presidente C', 420),
(45, 'Presidente B', 460),
(46, 'Presidente A', 500),
(47, 'Elite G', 550),
(48, 'Elite F', 600),
(49, 'Elite E', 650),
(50, 'Elite D', 700),
(51, 'Elite C', 750),
(52, 'Elite B', 800),
(53, 'Elite A', 850),
(54, 'Junta Directiva G', 1000),
(55, 'Junta Directiva F', 1100),
(56, 'Junta Directiva E', 1200),
(57, 'Junta Directiva D', 1300),
(58, 'Junta Directiva C', -1),
(59, 'Junta Directiva B', -1),
(60, 'Junta Directiva A', -1),
(61, 'Administrador', -1),
(62, 'Manager', -1),
(63, 'Founder', -1),
(64, 'Dueño', -1),
(65, 'Dueño WEB', -1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `saves`
--

CREATE TABLE `saves` (
  `id` int(11) NOT NULL,
  `id_dio` int(11) NOT NULL,
  `id_recibe` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `deleted_by` int(11) NOT NULL,
  `fecha_exp` date NOT NULL,
  `fecha_cierre` date NOT NULL,
  `extendido_por` int(11) NOT NULL,
  `tipo` int(11) NOT NULL COMMENT '0 > save 1 > fila 2 > VIP'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_amistad`
--

CREATE TABLE `solicitudes_amistad` (
  `id` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `fecha_aceptacion` datetime NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sugerencias`
--

CREATE TABLE `sugerencias` (
  `id` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `deleted` int(11) NOT NULL,
  `imagen` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `times`
--

CREATE TABLE `times` (
  `id` int(11) NOT NULL,
  `id_dio` int(11) NOT NULL,
  `id_recibe` int(11) NOT NULL,
  `is_timing` int(11) NOT NULL,
  `valid_timer` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `ended_at` datetime NOT NULL,
  `pagado` int(11) NOT NULL,
  `pagado_recibe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `times_admin`
--

CREATE TABLE `times_admin` (
  `id` int(11) NOT NULL,
  `id_dio` int(11) NOT NULL,
  `id_recibe` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `tipo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `habbo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `rol` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `firma` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `save_acumulado` int(11) NOT NULL,
  `tipo_acumulado` int(11) NOT NULL COMMENT '1 > pago 2> pago + boni',
  `especial` int(11) NOT NULL,
  `codigo` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `last_visit` datetime NOT NULL,
  `razon_despido` text COLLATE utf8_unicode_ci NOT NULL,
  `fecha_save_acumulado` datetime NOT NULL,
  `token_app` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `placa_paga` int(11) NOT NULL,
  `placa_boni` int(11) NOT NULL,
  `accepted_paga` int(11) NOT NULL,
  `accepted_boni` int(11) NOT NULL,
  `ip` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `user`, `password`, `habbo`, `rol`, `created_at`, `created_by`, `firma`, `save_acumulado`, `tipo_acumulado`, `especial`, `codigo`, `last_visit`, `razon_despido`, `fecha_save_acumulado`, `token_app`, `placa_paga`, `placa_boni`, `accepted_paga`, `accepted_boni`, `ip`) VALUES
(1, 'Admin', 'e10adc3949ba59abbe56e057f20f883e', 'Admin', 65, '2022-10-02 08:01:02', 1, 'ADM', 0, 0, 0, 'eadbc', '2022-10-02 08:01:02', '', '2022-10-02 08:01:02', '', 0, 0, 0, 0, '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `amigos`
--
ALTER TABLE `amigos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ascensos`
--
ALTER TABLE `ascensos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ascensos_admin`
--
ALTER TABLE `ascensos_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `atencion`
--
ALTER TABLE `atencion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configs`
--
ALTER TABLE `configs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `degrados`
--
ALTER TABLE `degrados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `imagenes_atencion`
--
ALTER TABLE `imagenes_atencion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagas`
--
ALTER TABLE `pagas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `peticiones`
--
ALTER TABLE `peticiones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `saves`
--
ALTER TABLE `saves`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `solicitudes_amistad`
--
ALTER TABLE `solicitudes_amistad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `times`
--
ALTER TABLE `times`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `times_admin`
--
ALTER TABLE `times_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `amigos`
--
ALTER TABLE `amigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ascensos`
--
ALTER TABLE `ascensos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ascensos_admin`
--
ALTER TABLE `ascensos_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `atencion`
--
ALTER TABLE `atencion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configs`
--
ALTER TABLE `configs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `degrados`
--
ALTER TABLE `degrados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `imagenes_atencion`
--
ALTER TABLE `imagenes_atencion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pagas`
--
ALTER TABLE `pagas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `peticiones`
--
ALTER TABLE `peticiones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `saves`
--
ALTER TABLE `saves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_amistad`
--
ALTER TABLE `solicitudes_amistad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sugerencias`
--
ALTER TABLE `sugerencias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `times`
--
ALTER TABLE `times`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `times_admin`
--
ALTER TABLE `times_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
