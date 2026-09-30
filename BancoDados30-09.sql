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
-- Table structure for table `instrucao`
--

DROP TABLE IF EXISTS `instrucao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instrucao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `instrucao` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `id_peca` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instrucao`
--

LOCK TABLES `instrucao` WRITE;
/*!40000 ALTER TABLE `instrucao` DISABLE KEYS */;
INSERT INTO `instrucao` VALUES (1,'Inspecionar canaletas dos anéis de segmento. ou coisas assim','1'),(2,'Checar alinhamento do pino de pistão.','1'),(3,'Verificar ausência de riscos na saia do pistão.','1'),(4,'Verificar espessura do material de atrito.','2'),(5,'Inspecionar trincas ou trincamentos na superfície.','2'),(6,'Conferir assentamento das placas antirruído.','2'),(7,'Verificar desgaste uniforme das sapatas.','2'),(8,'Verificar curso e velocidade de retorno da haste.','3'),(9,'Checar vazamento de fluido hidráulico.','3'),(10,'Conferir estado e desgaste das buchas de fixação.','3'),(11,'Inspecionar alinhamento da coifa e do batente.','3'),(12,'Verificar alinhamento e fixação das presilhas.','4'),(13,'Checar pintura, tonalidade e acabamento externo.','4'),(14,'Conferir absorvedor de impacto interno.','4'),(15,'Verificar encaixe das grades e faróis de milha.','4'),(31,'Verificar carga e tensão da bateria.','5'),(32,'Conferir nível do eletrólito nas células.','5'),(33,'Checar vedação e ausência de corrosão nos bornes.','5'),(34,'Testar corrente de partida a frio (CCA).','5'),(35,'Verificar carga e tensão da bateria.','5'),(36,'Conferir nível do eletrólito nas células.','5'),(37,'Checar vedação e ausência de corrosão nos bornes.','5'),(38,'Testar corrente de partida a frio (CCA).','5'),(40,'crie um acerelador de particulas','1');
/*!40000 ALTER TABLE `instrucao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pecas`
--

DROP TABLE IF EXISTS `pecas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pecas` (
  `nome_tipo` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `grupo_peca` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `ID` int NOT NULL AUTO_INCREMENT,
  `quantidade_pecas` int NOT NULL,
  `pecas_aprovadas` int DEFAULT NULL,
  `lote` int DEFAULT NULL,
  `pecas_reprovadas` int DEFAULT NULL,
  `id_pecas` int DEFAULT NULL,
  `data_insp` datetime DEFAULT CURRENT_TIMESTAMP,
  `usuario` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `funcao` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `id_pecas` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pecas`
--

LOCK TABLES `pecas` WRITE;
/*!40000 ALTER TABLE `pecas` DISABLE KEYS */;
INSERT INTO `pecas` VALUES ('Cinto de Segurança','Componentes de Segurança',6,0,NULL,NULL,NULL,6,'2026-09-09 15:27:28','','',0),('pistao','Motor e Transmissão',7,100,70,3,30,1,'2026-09-09 16:09:43','','',0),('pistao','Motor e Transmissão',8,100,50,3,50,1,'2026-09-09 16:09:57','','',0),('pistao','Motor e Transmissão',9,300,299,5,1,1,'2026-09-09 16:10:51','','',0),('amortecedor','Suspensão e Direção',10,500,300,1,200,3,'2026-09-09 16:11:11','','',0),('amortecedor','Suspensão e Direção',11,500,300,1,200,3,'2026-09-09 16:17:13','','',0),('amortecedor','Suspensão e Direção',12,500,300,1,200,3,'2026-09-09 16:17:21','','',0),('amortecedor','Suspensão e Direção',13,500,300,1,200,3,'2026-09-09 16:17:34','','',0),('amortecedor','Suspensão e Direção',14,500,300,1,200,3,'2026-09-09 16:22:51','','',0),('amortecedor','Suspensão e Direção',15,500,300,1,200,3,'2026-09-09 16:22:54','','',0),('para_choque','Carroceria/Acabamento',16,100,70,386,30,4,'2026-09-09 16:24:04','','',0),('pastilha','Freios',17,22,13,3,9,2,'2026-09-09 16:29:49','','',0),('pastilha','Freios',18,22,13,3,9,2,'2026-09-09 16:35:56','','',0),('pistao','Motor e Transmissão',19,100,200,3,-100,1,'2026-09-09 16:36:32','','',0),('pistao','Motor e Transmissão',20,100,200,3,-100,1,'2026-09-09 16:36:50','','',0),('pistao','Motor e Transmissão',21,100,200,3,-100,1,'2026-09-09 16:37:30','','',0),('pastilha','Freios',22,33,33,33,0,2,'2026-09-09 16:38:32','','',0),('pastilha','Freios',23,33,33,33,0,2,'2026-09-09 16:39:05','','',0),('pistao','Motor e Transmissão',24,200,150,3,50,1,'2026-09-09 16:39:45','','',0),('pistao','Motor e Transmissão',25,200,150,3,50,1,'2026-09-09 16:39:56','','',0),('pistao','Motor e Transmissão',26,200,150,3,50,1,'2026-09-09 16:41:38','','',0),('pastilha','Freios',27,100,50,22,50,2,'2026-09-09 16:41:57','','',0),('bateria','Elétrica',28,0,0,0,0,4,'2026-09-16 14:20:00','','',0),('bateria','Elétrica',29,0,0,0,0,4,'2026-09-16 14:20:04','','',0),('bateria','Elétrica',30,300,250,4,50,5,'2026-09-16 14:36:10','','',0),('pastilha','Freios',36,23,23,23,0,2,'2026-09-16 14:53:08','','',0),('pastilha','Freios',37,23,23,23,0,2,'2026-09-16 14:59:26','','',0),('para_choque','Carroceria/Acabamento',38,100,100,10,0,4,'2026-09-16 15:00:10','','',0),('pistao','Motor e Transmissão',39,23,22,3,1,1,'2026-09-16 16:04:15','','',0),('para_choque','Carroceria/Acabamento',40,10,10,3,0,4,'2026-09-16 16:19:57','Ana','',0),('para_choque','Carroceria/Acabamento',41,21,20,5,1,4,'2026-09-16 16:24:06','Ana','Administrador',0),('pistao','Motor e Transmissão',42,56,12,564,44,1,'2026-09-23 08:15:21','Ana','Administrador',0),('para_choque','Carroceria/Acabamento',43,100,90,3,10,4,'2026-09-23 11:56:01','Ana','Administrador',0),('para_choque','Carroceria/Acabamento',44,100,70,4,30,4,'2026-09-23 12:00:49','Ana','Administrador',0),('pistao','Motor e Transmissão',45,386,386,386,0,1,'2026-09-29 11:53:18','AdalbertoBerto','Administrador',0),('amortecedor','Suspensão e Direção',46,386,100,333,286,3,'2026-09-29 12:14:42','AdalbertoBerto','Administrador',4),('amortecedor','Suspensão e Direção',47,233,222,12,11,3,'2026-09-29 13:57:48','marlon jacksin','Funcionario',27),('pistao','Motor e Transmissão',48,400,400,4,0,1,'2026-09-30 08:38:38','Ana','Administrador',9),('pastilha','Freios',97,100,70,386,30,4,'2025-12-09 15:27:28','teste1','funcionario',386),('pastilha','Freios',98,100,70,387,30,4,'2026-03-09 15:27:28','teste1','funcionario',386),('pastilha','Freios',99,100,70,386,30,4,'2026-06-09 15:27:28','teste1','funcionario',386);
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
  `setor_Funcionario` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'0','admin','admin@email.com','$2y$10$ftT51sdnNjQatR6q3XjhaOf7IYoRn3bWAiVS3BswR/lUZdkmX14su',1,'0',''),(4,'12332112332','AdalbertoBerto','adalberto@gmail.com','$2y$10$UC9wbmVOoLhyooquG46J9.JY/oTIIsS.mG0teb/z3hRl6II1BEas2',1,'12332112332',''),(5,'12345676543','Tonykillrecords','tony@gmail.com','$2y$10$PysbqBCzaI6BcTdOe2hApOn6.LpwlQK5xLzvnYzH8nqRWldhsxzZa',1,'23454323454',''),(9,'76543456765','Ana','ana@H.com.br','$2y$10$BjgaMn9RgP1VQxYuP.bHLeEoBOsRbIaVq1kw.oyTcEWV0Jo1/v3W.',1,'65434567654',''),(12,'12332131333','arlete silva gomes','arlete@gmail.com','$2y$10$t1cnOqIFgcvtwOuiIUzPUuagsa6WptILMeBcOA4aEMNGS9PfkAi1C',1,'12332112332',''),(27,'23456765432','marlon jacksin','maicholjaecosn@gmail.com','$2y$10$oBes/T9xWpMzTa90z8NB..63uc.xJ7ag.1uzIQtdy.6u2pNKBm/Ha',NULL,'12345678765','inspecao'),(28,'76543561234','teste','1233@gmail.com','$2y$10$Oa.8tcWzRa4tuwwbLrpoSOPxjSc6oTKZhNbV7sKWwx5aXjIJZeo3C',NULL,'12345678765','qualidade'),(33,'12345678900','teste386','testw386@gmail.com','$2y$10$JLEF6WEv0zKJK8xL30FnL.crIMwIq/pPOnkBWhD/AYrXocMGmfW..',NULL,'12345678987','qualidade');
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

-- Dump completed on 2026-09-30  9:49:30
