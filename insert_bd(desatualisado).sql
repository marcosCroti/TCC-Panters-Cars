-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: first_data
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `funcionario`
--

DROP TABLE IF EXISTS `funcionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionario` (
  `nome_fun` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `ID` int NOT NULL AUTO_INCREMENT,
  `CPF` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `setor` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `quantidade_pecas_no_dia` int NOT NULL,
  `status_fun` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionario`
--

LOCK TABLES `funcionario` WRITE;
/*!40000 ALTER TABLE `funcionario` DISABLE KEYS */;
INSERT INTO `funcionario` VALUES ('João Silva',1,'111.111.111-11','joao@empresa.com','Montagem',47,'Ativo'),('Maria Santos',2,'222.222.222-22','maria@empresa.com','Qualidade',35,'Ativo'),('Carlos Ramos',3,'333.333.333-33','carlos@empresa.com','Expedição',52,'Ativo'),('Ana Gabriela',4,'444.444.444-44','ana@empresa.com','Inspeção',28,'Inativo'),('Pedro Pimenteira',5,'555.555.555-55','pedro@empresa.com','Montagem',61,'Ativo');
/*!40000 ALTER TABLE `funcionario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pecas`
--

DROP TABLE IF EXISTS `pecas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pecas` (
  `ima_peca` blob NOT NULL,
  `nome_tipo` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `grupo_peca` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pecas` int NOT NULL AUTO_INCREMENT,
  `quantidade_pecas` int NOT NULL,
  `quantidade_pecas_no_dia` int NOT NULL,
  `local_peca` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `entrada_peca` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pecas`),
  UNIQUE KEY `id_pecas` (`id_pecas`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pecas`
--

LOCK TABLES `pecas` WRITE;
/*!40000 ALTER TABLE `pecas` DISABLE KEYS */;
INSERT INTO `pecas` VALUES (_binary 'img1','Pistão','Motor e Transmissão',1,0,0,'Setor 1','2026-06-03 14:42:15'),(_binary 'img2','Pastilhas','Freios',2,0,0,'Setor 1','2026-06-03 14:42:15'),(_binary 'img3','Amortecedores','Suspensão e Direção',3,0,0,'Setor 2','2026-06-03 14:42:15'),(_binary 'img4','Bateria','Elétrica',4,0,0,'Setor 2','2026-06-03 14:42:15'),(_binary 'img5','Para-choques','Carroceria/Acabamento',5,0,0,'Setor 3','2026-06-03 14:42:15'),(_binary 'img6','Cinto de Segurança','Componentes de Segurança',6,0,0,'Setor 3','2026-06-03 14:42:15');
/*!40000 ALTER TABLE `pecas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_peca`
--

DROP TABLE IF EXISTS `tipo_peca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_peca` (
  `nome_tipo` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `id_pecas` int DEFAULT NULL,
  `ima_peca` blob NOT NULL,
  PRIMARY KEY (`nome_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_peca`
--

LOCK TABLES `tipo_peca` WRITE;
/*!40000 ALTER TABLE `tipo_peca` DISABLE KEYS */;
/*!40000 ALTER TABLE `tipo_peca` ENABLE KEYS */;
  UNLOCK TABLES;

  --
  -- Table structure for table `usuarios`
  --

  DROP TABLE IF EXISTS `usuarios`;
  /*!40101 SET @saved_cs_client     = @@character_set_client */;
  /*!50503 SET character_set_client = utf8mb4 */;
  CREATE TABLE `usuarios` (
    `id` int NOT NULL AUTO_INCREMENT,
    `CPF` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
    `usuario_nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
    `email` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
    `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `isAdmin` tinyint(1) DEFAULT NULL,
    `telefone` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  /*!40101 SET character_set_client = @saved_cs_client */;

  --
  -- Dumping data for table `usuarios`
  --

  LOCK TABLES `usuarios` WRITE;
  /*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
  INSERT INTO `usuarios` VALUES (1,'0','admin','admin@email.com','$2y$10$ftT51sdnNjQatR6q3XjhaOf7IYoRn3bWAiVS3BswR/lUZdkmX14su',1,'0'),(4,'12332112332','AdalbertoBerto','adalberto@gmail.com','$2y$10$UC9wbmVOoLhyooquG46J9.JY/oTIIsS.mG0teb/z3hRl6II1BEas2',1,'12332112332'),(5,'12345676543','Tonykillrecords','tony@gmail.com','$2y$10$PysbqBCzaI6BcTdOe2hApOn6.LpwlQK5xLzvnYzH8nqRWldhsxzZa',1,'19997816365'),(7,'09231697846','Marcola55','elpichula.croti@gmail.com','$2y$10$70CtLVyzVzFeAxaeMIHH8OCcXyBtP8CpJg3ZFl3GKtW1Dv0CXyLy.',NULL,'19997816365'),(9,'76543456765','Ana','ana@H.com.br','$2y$10$BjgaMn9RgP1VQxYuP.bHLeEoBOsRbIaVq1kw.oyTcEWV0Jo1/v3W.',1,'65434567654'),(12,'12332131333','arlete silva gomes','arlete@gmail.com','$2y$10$t1cnOqIFgcvtwOuiIUzPUuagsa6WptILMeBcOA4aEMNGS9PfkAi1C',1,'12332112332'),(14,'54312312312','Marcos Vinicius Rodrigues Croti','marcos.croti@aluno.senai.br','$2y$10$9xvdORwBudKwOht.ImHNBOJkwMwMvIfvRwacWIzbrAoLr6kwE.AUK',NULL,NULL),(15,'33322233322','Marcola233','marcola123@fmail.com','$2y$10$/bW4eEdxY1pDeDTxhNnIfuTF66vRpmRET/0USUsfYy7jxF7C8MRJ2',NULL,NULL),(17,'31231512412','Yuri Alberto','yuri@gmail.com','$2y$10$vSo8/NeplvJCPk58jv/Lfe956sQFMzvna0jacMJIrvBV7VwYp7L.m',NULL,NULL);
  /*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
  UNLOCK TABLES;
  /*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

  /*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
  /*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
  /*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
  /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
  /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
  /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
  /*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

  -- Dump completed on 2026-08-05 16:22:08
