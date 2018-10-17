-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.1.29-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para chacara
CREATE DATABASE IF NOT EXISTS `chacara` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `chacara`;

-- Copiando estrutura para tabela chacara.config
CREATE TABLE IF NOT EXISTS `config` (
  `CFG_DEBUG` varchar(50) DEFAULT NULL,
  `CFG_PORT` varchar(50) DEFAULT NULL,
  `CFG_BASE_ROUTE` varchar(50) DEFAULT NULL,
  `CFG_IP_SERVER` varchar(50) DEFAULT NULL,
  `CFG_CAMINHOLOGO` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela chacara.config: ~1 rows (aproximadamente)
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` (`CFG_DEBUG`, `CFG_PORT`, `CFG_BASE_ROUTE`, `CFG_IP_SERVER`, `CFG_CAMINHOLOGO`) VALUES
	('true', ':80', '/Chacara', '192.168.1.220', NULL);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;

-- Copiando estrutura para tabela chacara.historico_log
CREATE TABLE IF NOT EXISTS `historico_log` (
  `RECNO` bigint(20) NOT NULL AUTO_INCREMENT,
  `HLO_DATA` date DEFAULT NULL,
  `HLO_HORA` varchar(8) DEFAULT NULL,
  `USU_CODIGO` bigint(20) DEFAULT NULL,
  `HLO_USUARIO` varchar(50) DEFAULT NULL,
  `HLO_MODULO` varchar(150) DEFAULT NULL,
  `HLO_ACAO` varchar(500) DEFAULT NULL,
  `HLO_IP` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`RECNO`),
  KEY `USU_CODIGO` (`USU_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela chacara.historico_log: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `historico_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `historico_log` ENABLE KEYS */;

-- Copiando estrutura para tabela chacara.menus
CREATE TABLE IF NOT EXISTS `menus` (
  `RECNO` bigint(20) NOT NULL AUTO_INCREMENT,
  `MEN_MENU` varchar(150) DEFAULT NULL,
  `MEN_FORM` varchar(150) DEFAULT NULL,
  `MEN_DESCRICAO` varchar(150) DEFAULT NULL,
  `MEN_GRUPO` varchar(150) DEFAULT NULL,
  `MEN_GRUPO_ORDEM` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`RECNO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela chacara.menus: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;

-- Copiando estrutura para tabela chacara.permissao
CREATE TABLE IF NOT EXISTS `permissao` (
  `RECNO` bigint(20) NOT NULL AUTO_INCREMENT,
  `USU_CODIGO` bigint(20) DEFAULT NULL,
  `FORM` varchar(150) DEFAULT NULL,
  `MENU` varchar(150) DEFAULT NULL,
  `FORMEXTENSO` varchar(250) DEFAULT NULL,
  `ACESSO` varchar(1) DEFAULT NULL,
  `INCLUIR` varchar(1) DEFAULT NULL,
  `CORRIGIR` varchar(1) DEFAULT NULL,
  `EXCLUIR` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`RECNO`),
  KEY `USU_CODIGO` (`USU_CODIGO`),
  CONSTRAINT `USU_CODIGO` FOREIGN KEY (`USU_CODIGO`) REFERENCES `usuarios` (`USU_CODIGO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela chacara.permissao: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `permissao` DISABLE KEYS */;
/*!40000 ALTER TABLE `permissao` ENABLE KEYS */;

-- Copiando estrutura para tabela chacara.recnos
CREATE TABLE IF NOT EXISTS `recnos` (
  `RECNO` bigint(20) DEFAULT NULL,
  `TABELA` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela chacara.recnos: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `recnos` DISABLE KEYS */;
/*!40000 ALTER TABLE `recnos` ENABLE KEYS */;

-- Copiando estrutura para tabela chacara.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `RECNO` bigint(20) NOT NULL AUTO_INCREMENT,
  `USU_CODIGO` bigint(20) DEFAULT NULL,
  `USU_LOGIN` varchar(50) DEFAULT NULL,
  `USU_SENHA` varchar(50) DEFAULT NULL,
  `USU_NOME` varchar(300) DEFAULT NULL,
  `USU_EMAIL` varchar(300) DEFAULT NULL,
  `USU_CARGO` varchar(300) DEFAULT NULL,
  `USU_ADMIN` varchar(1) DEFAULT NULL,
  `USU_PRIMEIROACESSO` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`RECNO`),
  KEY `USU_CODIGO` (`USU_CODIGO`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela chacara.usuarios: ~1 rows (aproximadamente)
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` (`RECNO`, `USU_CODIGO`, `USU_LOGIN`, `USU_SENHA`, `USU_NOME`, `USU_EMAIL`, `USU_CARGO`, `USU_ADMIN`, `USU_PRIMEIROACESSO`) VALUES
	(1, 1, 'davi', 'D10', 'Davi Cervellini', 'davi20012@gmail.com', 'Admin', '1', '1');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
