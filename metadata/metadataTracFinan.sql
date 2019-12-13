-----------------------------------------------------------
-- TABELA           | RES | DIR | TRG | ESP | F10  | FORM |
-----------------------------------------------------------
-- AGENDA           | AGE | 32  |     |     |      |      |   
-- AGENDATAREFA     | AT  | 18  |     |     |      |      |
-- ALIQUOTASIMPLES  | AS  | 17  |     |     |      |      |
-- BALANCO          | BLN | 13  |     |     |      |      |
-- BANCO            | BNC | 06  |     |     |      |      |
-- BANCOCODIGO      | BCD | 06  |     |     |      |      |
-- BANCOSTATUS      | BST | 06  |     |     |      |      |
-- CARGO            | CRG | 01  |     |     |      |      |
-- CATEGORIA        | CTG | 12  |     |     |      |      |
-- CIDADE           | CDD | 08  |     |     |      |      |
-- CONTACONTABIL    | CC  | 13  |     |     |      |      |
-- CONTARESUMO      | CTR | 13  |     |     |      |      |
-- CFOP             | CFO | 14  |     |     |      |      |
-- CNABARQUIVO      | ARQ | 10  |     |     |      |      |
-- CNABENVIO        | ENV | 10  |     |     |      |      |
-- CNABERRO         | ERR | 30  |     |     |      |      |
-- CNABINSTRUCAO    | CI  | 30  |     |     |      |      |
-- CNABLAYOUT       | LAY | 30  |     |     |      |      |
-- CNABRETORNO      | CR  | 30  |     |     |      |      |
-- COMPETENCIA      | CMP | 07  |     |     |      |      |
-- CONTADOR         | CNT | 03  |     |     |      |      |
-- CONTRATO         | CTT | 15  |     |     |      |      |
-- CSTICMS          | ICMS| 14  |     |     |      |      |
-- CSTIPI           | IPI | 14  |     |     |      |      |
-- CSTPIS           | PIS | 14  |     |     |      |      |
-- CSTSIMPLES       | SN  | 14  |     |     |      |      |
-- EMAIL            | EMA | 16  |     |     |      |      |
-- EMBALAGEM        | EMB | 24  |     |     |      |      |
-- EMPRESA          | EMP | 03  |     |     |      |      |
-- EMPRESARAMO      | ERM | 25  |     |     |      |      |
-- EMPRESAREGTRIB   | ERT | 25  |     |     |      |      |
-- EMPRESATIPO      | ETP | 25  |     |     |      |      |
-- EMPRESATRIBFED   | ETF | 25  |     |     |      |      |
-- ESTADO           | EST | 08  |     |     |      |      |
-- FAVORECIDO       | FVR | 05  |     |     |      |      |
-- FERIADO          | FRD | 19  |     |     |      |      |
-- FILIAL           | FLL | 03  |     |     |      |      |
-- FORMACOBRANCA    | FC  | 20  |     |     |      |      |
-- GRUPOFAVORECIDO  | GF  | 11  |     |     |      |      |
-- IMPOSTO          | IMP | 23  |     |     |      |      |
-- LOGRADOURO       | LGR | 08  |     |     |      |      |
-- MOEDA            | MOE | 08  |     |     |      |      |
-- NATUREZAOPERACAO | NO  | 14  |     |     |      |      |
-- NCM              | NCM | 14  |     |     |      |      |
-- NFPRODUTO        | NFP | 26  |     |     |      |      |
-- NFSERVICO        | NFS | 27  |     |     |      |      |
-- PADRAO           | PDR | 10  |     |     |      |      |
-- PADRAOGRUPO      | PG  | 10  |     |     |      |      |
-- PADRAOTITULO     | PT  | 10  |     |     |      |      |
-- PAGAR            | PGR | 28  |     |     |      |      |
-- PAGARTITULO      | PTT | 25  |     |     |      |      |
-- PAGARTIPO        | PTP | 25  |     |     |      |      |
-- PAIS             | PAI | 08  |     |     |      |      |
-- PRODUTO          | PRD | 09  |     |     |      |      |
-- PRODUTOORIGEM    | PO  | 09  |     |     |      |      |
-- QUALIFICACAOCONT | QC  | 03  |     |     |      |      |
-- R10FAVORECIDO    | R10 | 05  |     |     |      |      |
-- R02CONTRATO      | R02 | 15  |     |     |      |      |
-- R03CONTRATO      | R03 | 15  |     |     |      |      |
-- R07NFPRODUTO     | R07 | 26  |     |     |      |      |
-- R10NFSERVICO     | R10 | 27  |     |     |      |      |
-- RATEIO           | RAT | 28  |     |     |      |      |
-- REGIAO           | REG | 08  |     |     |      |      |
-- SERIENF          | SNF | 22  |     |     |      |      |
-- SERVICO          | SRV | 04  |     |     |      |      |
-- SERVICOPREFEITURA| SPR | 04  |     |     |      |      |
-- SPED             | SPD | 12  |     |     |      |      |
-- TIPODOCUMENTO    | TD  | 20  |     |     |      |      |
-- TRANSPORTADORA   | TRN | 21  |     |     |      |      |
-- USUARIO          | USR | 01  |     |     |      |      | 
-- USUARIOEMPRESA   | UE  | 02  |     |     |      |      |
-- USUARIOPERFIL    | UP  | 01  |     |     |      |      |
-- USUARIOSISTEMA   | US  | **  |     |     |      |      |
-- VENDEDOR         | VND | 29  |     |     |      |      |
--
-- DIREITO 01 USUARIO/USUARIOPERFIL/CARGO
--         02 USUARIOEMPRESA
--         03 EMPRESA/FILIAL/CONTADOR/QUALIFICACAOCONT
--         04 SERVICO/SERVICOPREFEITURA
--         05 FAVORECIDO/R10FAVORECIDO
--         06 BANCO/BANCOCODIGO/BANCOSTATUS
--         07 COMPETENCIA
--         08 CIDADE/REGIAO/ESTADO/PAIS/LOGRADOURO/MOEDA
--         09 PRODUTO
--         10 PADRAO/PADRAOGRUPO/PADRAOTITULO/CNABARQUIVO/CNABENVIO
--         11 GRUPOFAVORECIDO
--         12 CATEGORIA/SPED
--         13 CONTACONTABIL/CONTARESUMO/BALANCO
--         14 CFOP/CSTICMS/CSTIPI/CSTPIS/CSTSIMPLES/NCM/NATUREZAOPERACAO
--         15 CONTRATO/R02CONTRATO
--         16 EMAIL
--         17 ALIQUOTASIMPLES
--         18 AGENDATAREFA
--         19 FERIADO 
--         20 FORMACOBRANCA/TIPODOCUMENTO
--         21 TRANSPORTADORA
--         22 SERIENF
--         23 IMPOSTO
--         24 EMBALAGEM
--         25 EMPRESARAMO/EMPRESAREGTRIB/EMPRESATIPO/EMPRESATRIBFED/PAGARTITULO/PAGARTIPO
--         26 NFPRODUTO/R07NFPRODUTO
--         27 NFSERVICO/R10NFSERVICO
--         28 PAGAR/RATEIO
--         29 VENDEDOR
--         30 CNABERRO/CNABINSTRUCAO/CNABLAYOUT/CNABRETORNO
--         31 **TRANSFORMAR EM REGISTRO DO SISTEMA
--         31 AGENDA
-------------------------------------------------------------------------------------
--                                A G E N D A                                      --
--tblagenda
-------------------------------------------------------------------------------------
GO
CREATE TABLE AGENDA(
  AGE_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL 
  ,AGE_CODAT INTEGER NOT NULL
  ,AGE_RESPONSAVEL VARCHAR(15) NOT NULL
  ,AGE_VENCTO DATE NOT NULL
  ,AGE_DTCADASTRO DATE DEFAULT GETDATE() NOT NULL
  ,AGE_CADASTROU VARCHAR(15) NOT NULL
  ,AGE_DTBAIXA DATE
  ,AGE_CODEMP INTEGER NOT NULL
  ,AGE_REG VARCHAR(1) NOT NULL
  ,AGE_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_AgeReg CHECK( AGE_REG IN('A','P','S')));
GO
CREATE VIEW VAGENDA AS
  SELECT AGE_CODIGO,AGE_CODAT,AGE_RESPONSAVEL,AGE_VENCTO,AGE_DTCADASTRO
         ,AGE_CADASTROU,AGE_DTBAIXA,AGE_CODEMP,AGE_REG,AGE_CODUSR FROM AGENDA
GO
CREATE TABLE dbo.BKPAGENDA(
  AGE_ID INTEGER IDENTITY PRIMARY KEY NOT NULL 
  ,AGE_ACAO VARCHAR(1) NOT NULL
  ,AGE_DATA DATE DEFAULT GETDATE() NOT NULL
  ,AGE_CODIGO INTEGER NOT NULL 
  ,AGE_CODAT INTEGER NOT NULL
  ,AGE_RESPONSAVEL VARCHAR(15) NOT NULL
  ,AGE_VENCTO DATE NOT NULL
  ,AGE_DTCADASTRO DATE DEFAULT GETDATE() NOT NULL
  ,AGE_CADASTROU VARCHAR(15) NOT NULL
  ,AGE_DTBAIXA DATE
  ,AGE_CODEMP INTEGER NOT NULL
  ,AGE_REG VARCHAR(1) NOT NULL
  ,AGE_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_bkpAtdAcao CHECK( AT_ACAO IN('I','A','E'))  
);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- AGE_CODIGO     | PK  |    |    | INT                | Auto incremento
   -- AGE_CODAT      |     |    |    | INT NN             | Campo relacionado (AGENDATAREFA)
   -- AT_NOME        |     |    |    | VC(40) NN          | Campo relacionado (AGENDATAREFA)   
   -- AGE_RESPONSAVEL|     |    |    | VC(15) NN          | Nome do usuario responsavel soh ele/quem cadastrou pode alterar 
   -- AGE_VENCTO     |     |    |    | DAT NN             |
   -- AGE_DTCADASTRO | DEF |    |    | DAT NN             | Campo automatico
   -- AGE_CADASTROU  |     |    |    | VC(15) NN          | Nome do usuario que cadastrou
   -- AGE_DTBAIXA    |     |    |    | DAT                |
   -- AGE_CODE       | REL |    |    | INT NN             | Campo relacionado (EMPRESA)            
   -- EMP_APELIDO    | REL |    |    | VC(15)             | Campo relacionado (EMPRESA)               
   -- AGE_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- AGE_CODUSR     | REL |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D32         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--         
--         
-------------------------------------------------------------------------------------
--                              A G E N D A T A R E F A                            --
--tblagendatarefa
-------------------------------------------------------------------------------------
GO
CREATE TABLE AGENDATAREFA(
  AT_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,AT_NOME VARCHAR(20) NOT NULL
  ,AT_ATIVO VARCHAR(1) NOT NULL
  ,AT_REG VARCHAR(1) NOT NULL
  ,AT_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_AtAtivo CHECK( AT_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_AtReg CHECK( AT_REG IN('A','P','S')));
GO
CREATE VIEW VAGENDATAREFA AS
  SELECT AT_CODIGO,AT_NOME,AT_ATIVO,AT_REG,AT_CODUSR FROM AGENDATAREFA
GO  
INSERT INTO dbo.VAGENDATAREFA(AT_CODIGO,AT_NOME,AT_ATIVO ,AT_REG ,AT_CODUSR) VALUES(1,'CONFERIR EXTRATO BANCARIO'   ,'S'  ,'S'  ,1);
INSERT INTO dbo.VAGENDATAREFA(AT_CODIGO,AT_NOME,AT_ATIVO ,AT_REG ,AT_CODUSR) VALUES(2,'POSICAO DE TITULOS VENCIDOS' ,'S'  ,'S'  ,1);
INSERT INTO dbo.VAGENDATAREFA(AT_CODIGO,AT_NOME,AT_ATIVO ,AT_REG ,AT_CODUSR) VALUES(3,'INICIAR FATURAMENTO'         ,'S'  ,'S'  ,1);
INSERT INTO dbo.VAGENDATAREFA(AT_CODIGO,AT_NOME,AT_ATIVO ,AT_REG ,AT_CODUSR) VALUES(4,'PAGAMENTO ICMS'              ,'S'  ,'S'  ,1);
INSERT INTO dbo.VAGENDATAREFA(AT_CODIGO,AT_NOME,AT_ATIVO ,AT_REG ,AT_CODUSR) VALUES(5,'FOLHA DE PAGAMENTO'          ,'S'  ,'S'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- AT_CODIGO      | PK  |    |    | INT                |  Auto incremento
   -- AT_NOME        |     |    |    | VC(20) NN          |
   -- AT_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- AT_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- AT_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D18         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                      A L I Q U O T A S I M P L E S                              --
--tblaliquotasimples
-------------------------------------------------------------------------------------
GO
CREATE TABLE ALIQUOTASIMPLES(
  AS_ANEXO INTEGER NOT NULL
  ,AS_ITEM INTEGER NOT NULL
  ,AS_CODEMP INTEGER NOT NULL  
  ,AS_VLRINI NUMERIC(15,2) NOT NULL
  ,AS_VLRFIM NUMERIC(15,2) NOT NULL
  ,AS_ALIQUOTA NUMERIC(15,4) NOT NULL
  ,AS_IRPJ NUMERIC(15,4) NOT NULL
  ,AS_CSLL NUMERIC(15,4) NOT NULL
  ,AS_COFINS NUMERIC(15,4) NOT NULL
  ,AS_PIS NUMERIC(15,4) NOT NULL
  ,AS_CPP NUMERIC(15,4) NOT NULL
  ,AS_ICMS NUMERIC(15,4) NOT NULL
  ,AS_IPI NUMERIC(15,4) NOT NULL
  ,AS_ISS NUMERIC(15,4) NOT NULL
  ,AS_REG VARCHAR(1) NOT NULL
  ,AS_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_AsReg CHECK( AS_REG IN('A','P','S'))
  ,CONSTRAINT PKALIQUOTASIMPLES PRIMARY KEY(AS_ANEXO,AS_ITEM,AS_CODEMP));  
GO  
CREATE VIEW VALIQUOTASIMPLES AS
  SELECT AS_ANEXO,AS_ITEM,AS_CODEMP,AS_VLRINI,AS_VLRFIM,AS_ALIQUOTA,AS_IRPJ,AS_CSLL,AS_COFINS
         ,AS_PIS,AS_CPP,AS_ICMS,AS_IPI,AS_ISS,AS_REG,AS_CODUSR FROM ALIQUOTASIMPLES
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- AS_ANEXO       | PK  |    |    | INT                | 
   -- AS_ITEM        | PK  |    |    | INT                |    
   -- AS_CODEMP      | PK  |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- AS_VLRINI      |     |    |    | NUM(15,2) NN       |
   -- AS_VLRFIM      |     |    |    | NUM(15,2) NN       |
   -- AS_ALIQUOTA    |     |    |    | NUM(15,4) NN       |
   -- AS_IRPJ        |     |    |    | NUM(15,4) NN       |
   -- AS_CSLL        |     |    |    | NUM(15,4) NN       |
   -- AS_COFINS      |     |    |    | NUM(15,4) NN       |
   -- AS_PIS         |     |    |    | NUM(15,4) NN       |
   -- AS_CPP         |     |    |    | NUM(15,4) NN       |
   -- AS_ICMS        |     |    |    | NUM(15,4) NN       |
   -- AS_IPI         |     |    |    | NUM(15,4) NN       |
   -- AS_ISS         |     |    |    | NUM(15,4) NN       |
   -- AS_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- AS_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D17         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--         
-------------------------------------------------------------------------------------
--                              B A L A N C O                                      --
--tblbalanco
-------------------------------------------------------------------------------------
CREATE TABLE BALANCO(
  BLN_CODIGO VARCHAR(15) PRIMARY KEY NOT NULL
  ,BLN_NOME VARCHAR(40) NOT NULL
  ,BLN_CODSPD VARCHAR(2) NOT NULL
  ,BLN_ATIVO VARCHAR(1) NOT NULL
  ,BLN_REG VARCHAR(1) NOT NULL
  ,BLN_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_BlnAtivo CHECK( BLN_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_BlnReg CHECK( BLN_REG IN('A','P','S')));
GO
CREATE VIEW VBALANCO AS
  SELECT BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR FROM BALANCO
GO
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.'              ,'ATIVO'                                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.'           ,'CIRCULANTE'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.01.'        ,'DISPONIBILIDADES'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.01.01.'     ,'CAIXA'                                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.01.01.0001' ,'FUNDO FIXO'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.01.02.'     ,'FUNDO FIXO'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.01.02.0001' ,'BANCO'                                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.'        ,'CREDITOS'                                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.01.'     ,'ADIANTAMENTOS A FORNECEDORES'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.01.0001' ,'ADIANTAMENTOS A FORNECEDORES'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.02.'     ,'CLIENTES'                                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.02.0001' ,'CLIENTES A RECEBER'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.'     ,'IMPOSTOS E CONTRIB A RECUPERAR'           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0001' ,'PIS A RECUPERAR'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0002' ,'COFINS A RECUPERAR'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0003' ,'CSLL A RECUPERAR'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0004' ,'IRRF A RECUPERAR'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0005' ,'ISS A RECUPERAR'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0006' ,'INSS A RECUPERAR'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0007' ,'IPI A RECUPERAR'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0008' ,'SIMPLES A RECUPERAR'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0009' ,'ICMS A RECUPERAR'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0010' ,'COMPENSACAO DE IMPOSTOS E TAXAS'          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0011' ,'(-) PIS APURCAO'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0012' ,'(-) COFINS APURACAO'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0013' ,'(-) CSLL APURACAO'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0014' ,'(-) IRRF APURACAO'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0015' ,'(-) ISS APURACAO'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.05.0016' ,'(-) INSS APURACAO'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.07.'     ,'OUTRAS'                                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.01.05.07.0001' ,'OUTRAS CONTAS A RECEBER'                  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.'           ,'REALIZAVEL A LONGO PRAZO'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.'        ,'CREDITOS'                                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.04.'     ,'DEPOSITOS JUDICIAIS'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.04.0001' ,'DEPOSITOS JUDICIAIS - FISCAIS'            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.04.0002' ,'DEPOSITOS JUDICIAIS - TRABALHISTAS'       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.04.0003' ,'DEPOSITOS JUDICIAIS - CIVEIS'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.'     ,'OUTRAS CONTAS'                            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0001' ,'EMPRESTIMOS A RECEBER'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0002' ,'ADIANTAMENTO DE FUNCIONARIOS'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0003' ,'ADIANTAMENTO DE FERIAS'                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0004' ,'ADIANTAMENTO DE 13. SALARIO'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0005' ,'MUTUOS A RECEBER'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0006' ,'COMPENSACAO DE FOLHA DE PAGAMENTO'        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0007' ,'COMPENSACAO DE FOLHA DE EMPRESTIMOS'      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0008' ,'COMPENSACAO DE ENTRADAS E SAIDAS'         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0009' ,'COMPENSACAO DE MOV JUDICAL'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.03.01.08.0010' ,'TRANSFERENCIA ENTRE CONTAS'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.'           ,'PERMANENTE'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.01.'        ,'INVESTIMENTOS'                            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.01.01.'     ,'PARTICIPACOES PERM EM COLIG OU CONT'      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.01.01.0001' ,'INVESTIMENTOS EM EMPRESAS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.'        ,'IMOBILIZADO'                              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.01.'     ,'TERRENOS'                                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.01.0001' ,'IMOVEIS'                                  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.03.'     ,'EQUIPAMENTOS, MAQ E INST INDUSTRIAIS'     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.03.0001' ,'EQUIPAMENTOS DE INFORMATICA'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.03.0002' ,'EQUIPAMENTOS DIVERSOS'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.04.'     ,'VEiCULOS'                                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.04.0001' ,'VEICULOS'                                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.05.'     ,'MOVEIS, UTENSiLIOS E INST COMERCIAIS'     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.05.0001' ,'MOVEIS E UTENSILIOS'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.09.'     ,'OUTRAS IMOBILIZAcoES'                     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.09.0001' ,'BENFEITORIAS EM IMOVEIS DE TERCEIROS'     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.03.09.0002' ,'BENFEITORIAS EM BENS DE TERCEIROS'        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.05.'        ,'INTANGIVEL'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.05.09.'     ,'SOFTWARE OU PROGRAMAS DE COMPUTADOR'      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.05.09.0001' ,'INVESTIMENTOS EM SOFTWARE'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.05.09.0002' ,'INVESTIMENTOS EM PORTAL DE VENDAS'        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.05.13.'     ,'DESENVOLVIMENTO DE PRODUTOS'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.05.13.0001' ,'INVESTIMENTOS EM CAPACIT PROFISSIONAL'    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('1.05.05.13.0002' ,'INVESTIMENTO EM MARCAS/BRANDING'          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.'              ,'PASSIVO'                                  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.'           ,'CIRCULANTE'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.'        ,'OBRIGACOES DE CURTO PRAZO'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.01.'     ,'FORNECEDORES'                             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.01.0001' ,'CONTAS A PAGAR'                           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.03.'     ,'FINANCIAMENTOS A CURTO PRAZO'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.03.0001' ,'OUTROS CONTAS A PAGAR'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.'     ,'IMPOSTOS, TAXAS E CONTRIB A RECOLHER'     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0001' ,'PIS A PAGAR'                              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0002' ,'COFINS A PAGAR'                           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0003' ,'CONTRIBUICAO SOCIAL A PAGAR'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0004' ,'IMPOSTO DE RENDA A PAGAR'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0005' ,'ISS A PAGAR'                              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0006' ,'INSS A PAGAR'                             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0007' ,'IPI A PAGAR'                              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0008' ,'SIMPLES A PAGAR'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0009' ,'ICMS A PAGAR'                             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0010' ,'OUTRAS TAXAS E TRIBUTOS ESTAD A PAGAR'    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0011' ,'OUTRAS TAXAS E TRIBUTOS MUNICIP PAGAR'    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0012' ,'CONTRIBUICOES FEDERAIS RETIDAS A PAGAR'   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0013' ,'PARCELAMENTO REFIS A RECOLHER'            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0014' ,'PARCELAMENTO SIMPLES A RECOLHER'          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0015' ,'IR RETIDO A PAGAR'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0016' ,'ISS RETIDO A PAGAR'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.05.0017' ,'INSS RETIDO A PAGAR'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.07.'     ,'FGTS A RECOLHER'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.07.0001' ,'FGTS A PAGAR'                             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.11.'     ,'CONTRIB PREVIDENCIaRIAS A RECOLHER'       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.11.0001' ,'ENCARGOS SOCIAIS A PAGAR'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.11.0002' ,'IR RETIDO FOLHA A PAGAR'                  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.11.0003' ,'INSS RETIDO FOLHA A PAGAR'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.13.'     ,'SALaRIOS A PAGAR'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.13.0001' ,'SALARIOS A PAGAR'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.13.0002' ,'ESTAGIARIOS A PAGAR'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.25.'     ,'OUTRAS CONTAS'                            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.25.0001' ,'BENEFICIOS A PAGAR'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.25.0002' ,'FERIAS A PAGAR'                           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.25.0003' ,'13. SALARIO A PAGAR'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.25.0004' ,'RESCISOES A PAGAR'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.25.0005' ,'OUTROS PAGAMENTOS PESSOAL'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.01.01.25.0006' ,'PROLABORE A PAGAR'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.03.'           ,'EXIGIVELA LONGO PRAZO'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.03.01.'        ,'OBRIGACOES A LONGO PRAZO'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.03.01.03.'     ,'FINANCIAMENTOS A LONGO PRAZO'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.03.01.03.0001' ,'EMPRESTIMOS A PAGAR'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.03.01.03.0002' ,'MUTUOS A PAGAR'                           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.'           ,'PATRIMONIO LIQUIDO'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.01.'        ,'CAPITAL REALIZADO'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.01.01.'     ,'CAPITAL SUBSC DE DOMIC E RESID NO PAiS'   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.01.01.0001' ,'CAPITAL SOCIAL SUBSCRITO'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.01.03.'     ,'(-) CAPITAL A INT DE DOM E RESID NO PAIS' ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.01.03.0001' ,'(-) CAPITAL SOCIAL A INTEGRALIZAR'      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.03.'        ,'RESERVAS'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.03.01.'     ,'RESERVAS DE CAPITAL'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.03.01.0001' ,'APORTE DE CAPITAL'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.03.01.0002' ,'(-) RETIRADAS'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.03.01.0003' ,'COMPENSACAO DE SALDOS INICIAIS'         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.03.05.'     ,'RESERVAS DE LUCROS'                     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.03.05.0001' ,'LUCROS ACUMULADOS'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.03.05.0002' ,'PREJUIZOS ACUMULADOS'                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('2.05.03.05.0003' ,'RESULTADO DO EXERCICIO'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.'              ,'CUSTOS'                                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.'           ,'CUSTO DOS BENS E SERVICOS PRODUZIDOS'   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.'        ,'CUSTO DOS SERVICOS PRODUZIDOS'          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.03.'     ,'REMUN A DIRIG LIGADOS a PROD DE SERV'   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.03.0001' ,'PROLABORE'                              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.'     ,'CUSTO DO PESSOAL APLIC NA PROD DE SERV' ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0001' ,'SALARIOS'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0002' ,'ESTAGIARIOS'                            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0003' ,'FERIAS'                                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0004' ,'13. SALARIO'                            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0005' ,'RESCISOES A PAGAR'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0006' ,'OUTRAS DESPESAS COM PESSOAL'            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0007' ,'ACOES JUDICIAIS TRABALHISTAS'           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0008' ,'INSALUBRIDADE'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0009' ,'HORAS EXTRAS 100%'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0010' ,'HORAS EXTRAS 80%'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0011' ,'HORAS EXTRAS 60%'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0012' ,'FOLGA TRABALHADA'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.05.0013' ,'OUTROS PAGAMENTOS PESSOAL'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.15.'     ,'ENCARGOS SOCIAIS PREVIDeNCIA SOCIAL'    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.15.0001' ,'INSS'                                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.17.'     ,'ENCARGOS SOCIAIS FGTS'                  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.17.0001' ,'FGTS'                                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.19.'     ,'ENCARGOS SOCIAIS OUTROS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.19.0001' ,'ASSISTENCIA MEDICA'                     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.19.0002' ,'ASSISTENCIA ODONTOLOGICA'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.19.0003' ,'VALE TRANSPORTE'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.19.0004' ,'AUXILIOS DIVERSOS'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.19.0005' ,'CONTRIBUICOES SINDICAIS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.21.'     ,'ALIMENTAcaO DO TRABALHADOR'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.21.0001' ,'VALE REFEICAO'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('3.03.03.21.0002' ,'CESTA BASICA'                           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.'              ,'DESPESAS'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.'           ,'DESPESAS OPERAC DAS ATIVIDADES EM GERAL','**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.'        ,'DESPESAS OPERAC DAS ATIVIDADES EM GERAL','**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.'     ,'DESPESAS OPERAC DAS ATIVIDADES EM GERAL','**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0001' ,'ALUGUEL'                                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0002' ,'CONDOMINIO'                             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0003' ,'IPTU'                                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0004' ,'ENERGIA ELETRICA'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0005' ,'AGUA'                                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0006' ,'MANUTENCAO PREDIAL'                     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0007' ,'TELEFONIA E COMUNICACAO'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0008' ,'OUTRAS DESPESAS COM OCUPACAO'           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0009' ,'MATERIAL DE ESCRITORIO'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0010' ,'MATERIAL DE INFORMATICA'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0011' ,'IMPRESSOS DIVERSOS'                     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0012' ,'MATERIAIS DIVERSOS'                     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0013' ,'ASSESSORIA JURIDICA'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0014' ,'ASSESSORIA CONTABIL'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0015' ,'ASSESSORIA DE INFORMATICA'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0016' ,'ASSESSORIA FINANCEIRA'                  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0017' ,'ASSESSORIA DE DEPTO PESSOAL'            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0018' ,'ASSESSORIAS DIVERSAS'                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0019' ,'SERVICOS DE MOTOBOY E FRETES'           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0020' ,'MANUTENCAO DE EQUIPAMENTOS'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0021' ,'SERVICOS DE TERCEIROS DIVERSOS'         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0022' ,'HOSTING / SERVIDORES'                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0023' ,'EMAILS EXTERNOS / CLOUND'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0024' ,'FERRAMENTAS DE BUSCA WEB'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0025' ,'ANUNCIOS E MARKETING PERIODICOS'        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0026' ,'ANUNCIOS E MARKETING WEB'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0027' ,'ANUNCIOS E MARKETING MIDIA'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0028' ,'ASSESSORIA DE IMPRENSA'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0029' ,'REGISTRO DE MARCAS/PATENTES'            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0030' ,'PIS RETIDO DE TERCEIROS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0031' ,'COFINS RETIDO DE TERCEIROS'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0032' ,'CSLL RETIDO DE TERCEIROS'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0033' ,'IRRF RETIDO DE TERCEIROS'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0034' ,'ISS RETIDO DE TERCEIROS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0035' ,'INSS RETIDO DE TERCEIROS'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0036' ,'ACOES JUDICIAIS FISCAIS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0037' ,'ACOES JUDICIAIS CIVEIS'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0038' ,'DESPESA COM EVENTOS'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0039' ,'DESPESA COM VIAGENS'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0040' ,'DESPESAS COM PROPAGANDA'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0041' ,'DESPESAS COM MARCAS E PATENTES'         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0042' ,'BRINDES E DOACOES'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0043' ,'ASSINATURAS DE PERIODICOS'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0044' ,'COMBUSTIVEL'                            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0045' ,'PEDAGIOS'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0046' ,'DESPESA COM BENS DE PEQ VALOR'          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0047' ,'DESPESAS COM CARTORIOS'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0048' ,'DESPESAS COM VEICULOS'                  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0049' ,'COPA E COZINHA'                         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0050' ,'DESPESAS ADM DIVERSAS'                  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0051' ,'LOCACAO DE EQUIPAMENTOS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0052' ,'LOCACAO DE VEICULOS'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0053' ,'LEASING DE VEICULOS'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0054' ,'LEASING DE EQUIPAMENTOS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0055' ,'CPMF'                                   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0056' ,'TARIFAS BANCARIAS'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0057' ,'OUTRAS DESPESAS BANCARIAS'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0058' ,'OUTRAS RECEITAS BANCARIAS'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0059' ,'VARIACAO CAMBIAL ATIVA'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0060' ,'VARIACAO CAMBIAL PASSIVA'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0061' ,'DESP. CAMBIAIS DIVERSAS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0062' ,'JUROS RECEBIDOS APLICACOES FINANCEIRAS' ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0063' ,'JUROS RECEBIDOS EMPRESTIMOS TERCEIROS'  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0064' ,'JUROS RECEBIDOS CONTRATOS DE MUTUO'     ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0065' ,'JUROS RECEBIDOS OUTROS'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0066' ,'JUROS INCORRIDOS CAPITAL DE GIRO'       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0067' ,'JUROS INCORRIDOS EMPREST LONGO PRAZO'   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0068' ,'JUROS INCORRIDOS CONTA GARANTIDA'       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0069' ,'JUROS INCORRIDOS EMPRESTIMO TERCEIROS'  ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0070' ,'JUROS INCORRIDOS CONTRATOS DE MUTUO'    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0071' ,'JUROS INCORRIDOS CAPITAL PROPRIO'       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0072' ,'JUROS INCORRIDOS OUTROS'                ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0073' ,'DESP. FINANCEIRAS DIVERSAS'             ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0074' ,'OPERADORAS CARTOES DE CREDITO'          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0075' ,'OPERADORAS CARTOES DE DEBITO'           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0076' ,'OPERADORAS PAYPAL'                      ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('4.01.01.01.0077' ,'EMISSAO E CUSTODIA DE BOLETOS'          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.'              ,'RECEITAS'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.'           ,'RECEITAS'                               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.'        ,'RECEITA LIQUIDA'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.01.'     ,'RECEITA BRUTA'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.01.0001' ,'RECEITA DE SERVICOS'                    ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.02.'     ,'OUTRAS RECEITAS'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.02.0002' ,'OUTRAS RECEITAS'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.02.0003' ,'COMISSOES POR VENDAS / INTERMEDIACAO'   ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.'     ,'DEDUCOES DA RECEITA BRUTA'              ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0001' ,'PIS APURACAO'                           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0002' ,'COFINS APURACAO'                        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0003' ,'CSLL APURACAO'                          ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0004' ,'IMPOSTO DE RENDA'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0005' ,'ISS APURACAO'                           ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0006' ,'INSS APURACAO (FATURAMENTO)'            ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0007' ,'SIMPLES APURACAO'                       ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0008' ,'COMISSOES SOBRE VENDAS'                 ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0009' ,'COMISSOES PORTAL DE VENDAS WEB'         ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0010' ,'COMISSOES REPRESENTANTES'               ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0011' ,'CANCELAMENTO DE VENDAS/SERVICOS'        ,'**','S','P',1);
INSERT INTO VBALANCO(BLN_CODIGO,BLN_NOME,BLN_CODSPD,BLN_ATIVO,BLN_REG,BLN_CODUSR) VALUES('5.01.01.03.0012' ,'DEVOLUCAO DE VENDAS'                    ,'**','S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- BLN_CODIGO     | PK  |    |    | VC(15) NN          |
   -- BLN_NOME       |     |    |    | VC(40) NN          |
   -- BLN_CODSPD     | SEL |    |    | VC(2) NN           | Campo relacionado (SPED)  
   -- SPD_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (SPED)  
   -- BLN_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- BLN_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- BLN_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D13         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                B A N C O                                        --
--tblbanco
-------------------------------------------------------------------------------------
CREATE TABLE BANCO(
  BNC_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL 
  ,BNC_CODEMP INTEGER NOT NULL  
  ,BNC_NOME VARCHAR(40) NOT NULL  
  ,BNC_CODFVR INTEGER NOT NULL
  ,BNC_CODCC VARCHAR(15) NOT NULL             --CONTACONTABIL
  ,BNC_ENTRAFLUXO VARCHAR(1) NOT NULL
  ,BNC_CODBS VARCHAR(3) NOT NULL
  ,BNC_PADRAOFLUXO VARCHAR(1) NOT NULL
  ,BNC_CODBCD VARCHAR(6) NOT NULL             --BANCOCODIGO
  ,BNC_AGENCIA VARCHAR(8)
  ,BNC_AGENCIADV VARCHAR(1)
  ,BNC_CONTA VARCHAR(20)
  ,BNC_CONTADV VARCHAR(1)
  ,BNC_ATIVO VARCHAR(1) NOT NULL
  ,BNC_REG VARCHAR(1) NOT NULL
  ,BNC_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_BncEntraFluxo CHECK( BNC_ENTRAFLUXO IN('S','N'))
  ,CONSTRAINT chk_BncPadraoFluxo CHECK( BNC_PADRAOFLUXO IN('S','N'))  
  ,CONSTRAINT chk_BncAtivo CHECK( BNC_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_BncReg CHECK( BNC_REG IN('A','P','S')));
GO
CREATE VIEW VBANCO AS
  SELECT BNC_CODIGO
         ,BNC_CODEMP
         ,BNC_NOME
         ,BNC_CODFVR
         ,BNC_CODCC
         ,BNC_ENTRAFLUXO
         ,BNC_CODBS
         ,BNC_PADRAOFLUXO
         ,BNC_CODBCD
         ,BNC_AGENCIA
         ,BNC_AGENCIADV
         ,BNC_CONTA
         ,BNC_CONTADV
         ,BNC_ATIVO
         ,BNC_REG
         ,BNC_CODUSR
    FROM BANCO
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- BNC_CODIGO     | PK  |    |    | INT                |  Auto incremento
   -- BNC_NOME       |     |    |    | VC(40) NN          |
   -- BNC_CODFVR     | SEL |    |    | INT NN             | Campo relacionado (FAVORECIDO)
   -- FVR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (FAVORECIDO)   
   -- BNC_CODCC      | SEL |    |    | VC(15) NN          | Campo relacionado (CONTAGERENCIAL)
   -- CC_NOME        | SEL |    |    | VC(40) NN          | Campo relacionado (CONTAGERENCIAL)   
   -- BNC_ENTRAFLUXO | CC  |    |    | VC(1) NN           |
   -- BNC_CODBS      | SEL |    |    | VC(3) NN           | Campo relacionado (BANCOSTATUS)   
   -- BS_NOME        | SEL |    |    | VC(20) NN          | Campo relacionado (BANCOSTATUS)      
   -- BNC_PADRAOFLUXO| CC  |    |    | VC(1) NN           |  
   -- BNC_CODBCD     | SEL |    |    | VC(3) NN           | Campo relacionado (BANCOCODIGO)      
   -- BCD_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (BANCOCODIGO)         
   -- BNC_AGENCIA    |     |    |    | VC(8)              |
   -- BNC_AGENCIADV  |     |    |    | VC(1)              |
   -- BNC_CONTA      |     |    |    | VC(20)             |
   -- BNC_CONTADV    |     |    |    | VC(1)              |
   -- BNC_CODEMP     | SEL |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- BNC_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- BNC_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- BNC_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D06         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                                B A N C O C O D I G O                            --
--tblbancocodigo
-------------------------------------------------------------------------------------
CREATE TABLE BANCOCODIGO(
  BCD_CODIGO VARCHAR(6) PRIMARY KEY NOT NULL
  ,BCD_NOME VARCHAR(20) NOT NULL
  ,BCD_ATIVO VARCHAR(1) NOT NULL
  ,BCD_REG VARCHAR(1) NOT NULL
  ,BCD_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_BcdAtivo CHECK( BCD_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_BcdReg CHECK( BCD_REG IN('A','P','S')));
GO
CREATE VIEW VBANCOCODIGO AS
  SELECT BCD_CODIGO,BCD_NOME,BCD_ATIVO,BCD_REG,BCD_CODUSR FROM BANCOCODIGO
GO  
INSERT INTO dbo.VBANCOCODIGO(BCD_CODIGO,BCD_NOME,BCD_ATIVO ,BCD_REG ,BCD_CODUSR) VALUES('000','NAO SE APLICA'                 ,'S'  ,'S'  ,1);
INSERT INTO dbo.VBANCOCODIGO(BCD_CODIGO,BCD_NOME,BCD_ATIVO ,BCD_REG ,BCD_CODUSR) VALUES('036','BANCO BRADESCO BBI SA'         ,'S'  ,'S'  ,1);
INSERT INTO dbo.VBANCOCODIGO(BCD_CODIGO,BCD_NOME,BCD_ATIVO ,BCD_REG ,BCD_CODUSR) VALUES('745','BANCO CITIBANK SA'             ,'S'  ,'S'  ,1);
INSERT INTO dbo.VBANCOCODIGO(BCD_CODIGO,BCD_NOME,BCD_ATIVO ,BCD_REG ,BCD_CODUSR) VALUES('001','BANCO DO BRASIL SA'            ,'S'  ,'S'  ,1);
INSERT INTO dbo.VBANCOCODIGO(BCD_CODIGO,BCD_NOME,BCD_ATIVO ,BCD_REG ,BCD_CODUSR) VALUES('389','BANCO MERCANTIL DO BRASIL SA'  ,'S'  ,'S'  ,1);
INSERT INTO dbo.VBANCOCODIGO(BCD_CODIGO,BCD_NOME,BCD_ATIVO ,BCD_REG ,BCD_CODUSR) VALUES('623','BANCO PANAMERICANO SA'         ,'S'  ,'S'  ,1);
INSERT INTO dbo.VBANCOCODIGO(BCD_CODIGO,BCD_NOME,BCD_ATIVO ,BCD_REG ,BCD_CODUSR) VALUES('033','BANCO SANTANDER SA'            ,'S'  ,'S'  ,1);
INSERT INTO dbo.VBANCOCODIGO(BCD_CODIGO,BCD_NOME,BCD_ATIVO ,BCD_REG ,BCD_CODUSR) VALUES('341','ITAU UNIBANCO SA'              ,'S'  ,'S'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- BCD_CODIGO     | PK  |    |    | VC(06) NN          |
   -- BCD_NOME       |     |    |    | VC(20) NN          |
   -- BCD_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- BCD_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- BCD_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D06         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             B A N C O S T A T U S                               --
--tblbancostatus
-------------------------------------------------------------------------------------
CREATE TABLE BANCOSTATUS(
  BS_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,BS_NOME VARCHAR(20) NOT NULL
  ,BS_ATIVO VARCHAR(1) NOT NULL
  ,BS_REG VARCHAR(1) NOT NULL
  ,BS_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_BsAtivo CHECK( BS_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_BsReg CHECK( BS_REG IN('A','P','S')));
GO
CREATE VIEW VBANCOSTATUS AS
  SELECT BS_CODIGO,BS_NOME,BS_ATIVO,BS_REG,BS_CODUSR FROM BANCOSTATUS
GO  
INSERT INTO dbo.VBANCOSTATUS(BS_CODIGO,BS_NOME,BS_ATIVO ,BS_REG ,BS_CODUSR) VALUES('BCO','BANCO'            ,'S'  ,'S'  ,1);
INSERT INTO dbo.VBANCOSTATUS(BS_CODIGO,BS_NOME,BS_ATIVO ,BS_REG ,BS_CODUSR) VALUES('FF','FUNDOFIXO'         ,'S'  ,'S'  ,1);
INSERT INTO dbo.VBANCOSTATUS(BS_CODIGO,BS_NOME,BS_ATIVO ,BS_REG ,BS_CODUSR) VALUES('EF','EXTRA FINANCEIRO'  ,'S'  ,'S'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- BS_CODIGO      | PK  |    |    | VC(03) NN          |
   -- BS_NOME        |     |    |    | VC(20) NN          |
   -- BS_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- BS_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- BS_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D06         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                     C A R G O                                   --
--tblcargo
-------------------------------------------------------------------------------------
GO
CREATE TABLE dbo.CARGO(
  CRG_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,CRG_NOME VARCHAR(20) NOT NULL
  ,CRG_ATIVO VARCHAR(1) NOT NULL
  ,CRG_REG VARCHAR(1) NOT NULL
  ,CRG_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CrgAtivo CHECK( CRG_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CrgReg CHECK( CRG_REG IN('A','P','S'))
);
GO
CREATE VIEW VCARGO AS
  SELECT CRG_CODIGO,CRG_NOME,CRG_ATIVO,CRG_REG,CRG_CODUSR FROM CARGO
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- CRG_CODIGO     | PK  |    |    | VC(03) NN          |
   -- CRG_NOME       |     |    |    | VC(20) NN          |
   -- CRG_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CRG_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CRG_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D01         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--  
-------------------------------------------------------------------------------------
--                              C A T E G O R I A                                  --
--tblcategoria
-------------------------------------------------------------------------------------
GO
CREATE TABLE CATEGORIA(
  CTG_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,CTG_NOME VARCHAR(20) NOT NULL
  ,CTG_FISJUR VARCHAR(2) NOT NULL
  ,CTG_ATIVO VARCHAR(1) NOT NULL
  ,CTG_REG VARCHAR(1) NOT NULL
  ,CTG_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CtgFj CHECK( CTG_FISJUR IN('F','J','FJ'))  
  ,CONSTRAINT chk_CtgAtivo CHECK( CTG_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CtgReg CHECK( CTG_REG IN('A','P','S'))
);
GO
CREATE VIEW VCATEGORIA AS
  SELECT CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR FROM CATEGORIA
GO
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('ARM','ARMAZEM'            ,'J'  ,'S','P',1);
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('CER','CEREALISTA'         ,'FJ' ,'S','P',1);
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('CON','CONDOMINIO'         ,'J'  ,'S','P',1);
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('CF','CONSUMIDOR FINAL'    ,'FJ' ,'S','P',1);
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('EP','ENTE PUBLICO'        ,'J'  ,'S','P',1);
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('EXP','EXPORTADOR'         ,'J'  ,'S','P',1);
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('IND','INDUSTRIA'          ,'J'  ,'S','P',1);
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('NOR','NORMAL'             ,'J'  ,'S','P',1);
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('SIM','SIMPLES'            ,'J'  ,'S','P',1);
INSERT INTO VCATEGORIA(CTG_CODIGO,CTG_NOME,CTG_FISJUR,CTG_ATIVO,CTG_REG,CTG_CODUSR) VALUES('ZFM','ZONA FRANCA MANAUS' ,'J'  ,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- CTG_CODIGO     | PK  |    |    | VC(03) NN          |
   -- CTG_NOME       |     |    |    | VC(20) NN          |
   -- CTG_FISJUR     | CC  |    |    | VC(2) NN           | F|J  Se eh fisica ou juridica  
   -- CTG_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CTG_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CTG_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D12         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                 C I D A D E                                     --
--tblcidade
-------------------------------------------------------------------------------------
GO
CREATE TABLE CIDADE(
  CDD_CODIGO VARCHAR(7) PRIMARY KEY NOT NULL
  ,CDD_NOME VARCHAR(30) NOT NULL
  ,CDD_CODEST VARCHAR(3) NOT NULL
  ,CDD_DDD INTEGER DEFAULT 0 NOT NULL
  ,CDD_ATIVO VARCHAR(1) NOT NULL
  ,CDD_REG VARCHAR(1) NOT NULL
  ,CDD_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CddCodigo CHECK( CDD_CODIGO LIKE('[0-9][0-9][0-9][0-9][0-9][0-9][0-9]'))  
  ,CONSTRAINT chk_CddAtivo CHECK( CDD_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CddReg CHECK( CDD_REG IN('A','P','S'))
);
GO
CREATE VIEW VCIDADE AS
  SELECT CDD_CODIGO,CDD_NOME,CDD_CODEST,CDD_DDD,CDD_ATIVO,CDD_REG,CDD_CODUSR FROM CIDADE
GO
INSERT INTO VCIDADE VALUES('1200401','RIO BRANCO'      ,'AC',11,'S','P',1);
INSERT INTO VCIDADE VALUES('2704302','MACEIO'          ,'AL',11,'S','P',1);
INSERT INTO VCIDADE VALUES('1302603','MANAUS'          ,'AM',11,'S','P',1);
INSERT INTO VCIDADE VALUES('1600105','AMAPA'           ,'AP',11,'S','P',1);
INSERT INTO VCIDADE VALUES('2927408','SALVADOR'        ,'BA',11,'S','P',1);
INSERT INTO VCIDADE VALUES('2304400','FORTALEZA'       ,'CE',11,'S','P',1);
INSERT INTO VCIDADE VALUES('3205309','VITORIA'         ,'ES',11,'S','P',1);
INSERT INTO VCIDADE VALUES('5208707','GOIANIA'         ,'GO',11,'S','P',1);
INSERT INTO VCIDADE VALUES('2111300','SAO LUIS'        ,'MA',11,'S','P',1);
INSERT INTO VCIDADE VALUES('3106200','BELO HORIZONTE'  ,'MG',11,'S','P',1);
INSERT INTO VCIDADE VALUES('5002704','CAMPO GRANDE'    ,'MS',11,'S','P',1);
INSERT INTO VCIDADE VALUES('5103403','CUIABA'          ,'MT',11,'S','P',1);
INSERT INTO VCIDADE VALUES('2507507','JOAO PESSOA'     ,'PB',11,'S','P',1);
INSERT INTO VCIDADE VALUES('2611606','RECIFE'          ,'PE',11,'S','P',1);
INSERT INTO VCIDADE VALUES('2211001','TERESINA'        ,'PI',11,'S','P',1);
INSERT INTO VCIDADE VALUES('4106902','CURITIBA'        ,'PR',11,'S','P',1);
INSERT INTO VCIDADE VALUES('3304557','RIO DE JANEIRO'  ,'RJ',11,'S','P',1);
INSERT INTO VCIDADE VALUES('4314902','PORTO ALEGRE'    ,'RS',11,'S','P',1);
INSERT INTO VCIDADE VALUES('4209102','JOINVILLE'       ,'SC',11,'S','P',1);
INSERT INTO VCIDADE VALUES('2800308','ARACAJU'         ,'SE',11,'S','P',1);
INSERT INTO VCIDADE VALUES('3509502','CAMPINAS'        ,'SP',11,'S','P',1);
INSERT INTO VCIDADE VALUES('3516408','FRANCO DA ROCHA' ,'SP',11,'S','P',1);
INSERT INTO VCIDADE VALUES('3518800','GUARULHOS'       ,'SP',11,'S','P',1);
INSERT INTO VCIDADE VALUES('3520442','ILHA SOLTEIRA'   ,'SP',11,'S','P',1);
INSERT INTO VCIDADE VALUES('3543402','RIBEIRAO PRETO'  ,'SP',11,'S','P',1);
INSERT INTO VCIDADE VALUES('3550308','SAO PAULO'       ,'SP',11,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- CDD_CODIGO     | PK  |    |    | VC(07) NN          |
   -- CDD_NOME       |     |    |    | VC(30) NN          |
   -- CDD_CODEST     | SEL |    |    | VC(03) NN          | Campo relacionado (ESTADO)   
   -- EST_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (ESTADO)      
   -- CDD_DDD        |     |    |    | INT NN             |
   -- CDD_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CDD_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CDD_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D08         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--  
-------------------------------------------------------------------------------------
--                             C O N T A C O N T A B I L                           --
--tblcontagerencial
-------------------------------------------------------------------------------------
GO
CREATE TABLE CONTACONTABIL(
  CC_CODIGO VARCHAR(15) PRIMARY KEY NOT NULL
  ,CC_NOME VARCHAR(40) NOT NULL
  ,CC_LANCTO VARCHAR(1) NOT NULL
  ,CC_CODCTR VARCHAR(9) DEFAULT '0.00.0000' NOT NULL
  ,CC_ATIVO VARCHAR(1) NOT NULL
  ,CC_REG VARCHAR(1) NOT NULL
  ,CC_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CcCodigo CHECK( CC_CODIGO LIKE('[0-9][.][0-9][0-9][.][0-9][0-9][.][0-9][0-9][.][0-9][0-9][0-9][0-9]'))  
  ,CONSTRAINT chk_CcLancto CHECK( CC_LANCTO IN('S','N'))  
  ,CONSTRAINT chk_CcAtivo CHECK( CC_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CcReg CHECK( CC_REG IN('A','P','S')));
GO
CREATE VIEW VCONTACONTABIL AS
  SELECT CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR FROM CONTACONTABIL
GO
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.01.01.0001','FUNDO FIXO'                             ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.01.02.0001','BANCOS'                                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.01.0001','ADIANTAMENTOS A FORNECEDORES'           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.02.0001','CLIENTES A RECEBER'                     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0001','PIS A RECUPERAR'                        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0002','COFINS A RECUPERAR'                     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0003','CSLL A RECUPERAR'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0004','IRRF A RECUPERAR'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0005','ISS A RECUPERAR'                        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0006','INSS A RECUPERAR'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0007','IPI A RECUPERAR'                        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0008','SIMPLES A RECUPERAR'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0009','ICMS A RECUPERAR'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0010','COMPENSACAO DE IMPOSTOS E TAXAS'        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0011','(-) PIS APURCAO'                        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0012','(-) COFINS APURACAO'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0013','(-) CSLL APURACAO'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0014','(-) IRRF APURACAO'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0015','(-) ISS APURACAO'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.05.0016','(-) INSS APURACAO'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.01.05.07.0001','OUTRAS CONTAS A RECEBER'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.04.0001','DEPOSITOS JUDICIAIS - FISCAIS'          ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.04.0002','DEPOSITOS JUDICIAIS - TRABALHISTAS'     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.04.0003','DEPOSITOS JUDICIAIS - CIVEIS'           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0001','EMPRESTIMOS A RECEBER'                  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0002','ADIANTAMENTO DE FUNCIONARIOS'           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0003','ADIANTAMENTO DE FERIAS'                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0004','ADIANTAMENTO DE 13. SALARIO'            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0005','MUTUOS A RECEBER'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0006','COMPENSACAO DE FOLHA DE PAGAMENTO'      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0007','COMPENSACAO DE FOLHA DE EMPRESTIMOS'    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0008','COMPENSACAO DE ENTRADAS E SAIDAS'       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0009','COMPENSACAO DE MOV JUDICAL'             ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.03.01.08.0010','TRANSFERENCIA ENTRE CONTAS'             ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.01.01.0001','INVESTIMENTOS EM EMPRESAS'              ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.03.01.0001','IMOVEIS'                                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.03.03.0001','EQUIPAMENTOS DE INFORMATICA'            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.03.03.0002','EQUIPAMENTOS DIVERSOS'                  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.03.04.0001','VEICULOS'                               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.03.05.0001','MOVEIS E UTENSILIOS'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.03.09.0001','BENFEITORIAS EM IMOVEIS DE TERCEIROS'   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.03.09.0002','BENFEITORIAS EM BENS DE TERCEIROS'      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.05.09.0001','INVESTIMENTOS EM SOFTWARE'              ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.05.09.0002','INVESTIMENTOS EM PORTAL DE VENDAS'      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.05.13.0001','INVESTIMENTOS EM CAPACIT PROFISSIONAL'  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('1.05.05.13.0002','INVESTIMENTO EM MARCAS/BRANDING'        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.01.0001','CONTAS A PAGAR'                         ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.03.0001','OUTROS CONTAS A PAGAR'                  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.03.0002','ADIANTAMENTO CLIENTES'                  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0001','PIS A PAGAR'                            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0002','COFINS A PAGAR'                         ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0003','CONTRIBUICAO SOCIAL A PAGAR'            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0004','IMPOSTO DE RENDA A PAGAR'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0005','ISS A PAGAR'                            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0006','INSS A PAGAR'                           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0007','IPI A PAGAR'                            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0008','SIMPLES A PAGAR'                        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0009','ICMS A PAGAR'                           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0010','OUTRAS TAXAS E TRIBUTOS ESTAD A PAGAR'  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0011','OUTRAS TAXAS E TRIBUTOS MUNICIP PAGAR'  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0012','CONTRIBUICOES FEDERAIS RETIDAS A PAGAR' ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0013','PARCELAMENTO REFIS A RECOLHER'          ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0014','PARCELAMENTO SIMPLES A RECOLHER'        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0015','IR RETIDO A PAGAR'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0016','ISS RETIDO A PAGAR'                     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.05.0017','INSS RETIDO A PAGAR'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.07.0001','FGTS A PAGAR'                           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.11.0001','ENCARGOS SOCIAIS A PAGAR'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.11.0002','IR RETIDO FOLHA A PAGAR'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.11.0003','INSS RETIDO FOLHA A PAGAR'              ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.13.0001','SALARIOS A PAGAR'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.13.0002','ESTAGIARIOS A PAGAR'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.25.0001','BENEFICIOS A PAGAR'                     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.25.0002','FERIAS A PAGAR'                         ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.25.0003','13. SALARIO A PAGAR'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.25.0004','RESCISOES A PAGAR-PASSIVO'              ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.25.0005','OUTROS PAGAMENTOS PESSOAL-PASSIVO'      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.01.01.25.0006','PROLABORE A PAGAR'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.03.01.03.0001','EMPRESTIMOS A PAGAR'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.03.01.03.0002','MUTUOS A PAGAR'                         ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.05.01.01.0001','CAPITAL SOCIAL SUBSCRITO'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.05.01.03.0001','(-) CAPITAL SOCIAL A INTEGRALIZAR'      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.05.03.01.0001','APORTE DE CAPITAL'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.05.03.01.0002','(-) RETIRADAS'                          ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.05.03.01.0003','COMPENSACAO DE SALDOS INICIAIS'         ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.05.03.05.0001','LUCROS ACUMULADOS'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.05.03.05.0002','PREJUIZOS ACUMULADOS'                   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('2.05.03.05.0003','RESULTADO DO EXERCICIO'                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.03.0001','PROLABORE'                              ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0001','SALARIOS'                               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0002','ESTAGIARIOS'                            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0003','FERIAS'                                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0004','13. SALARIO'                            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0005','RESCISOES A PAGAR-CUSTO'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0006','OUTRAS DESPESAS COM PESSOAL'            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0007','ACOES JUDICIAIS TRABALHISTAS'           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0008','INSALUBRIDADE'                          ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0009','HORAS EXTRAS 100%'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0010','HORAS EXTRAS 80%'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0011','HORAS EXTRAS 60%'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0012','FOLGA TRABALHADA'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.05.0013','OUTROS PAGAMENTOS PESSOAL-CUSTO'        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.15.0001','INSS'                                   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.17.0001','FGTS'                                   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.19.0001','ASSISTENCIA MEDICA'                     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.19.0002','ASSISTENCIA ODONTOLOGICA'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.19.0003','VALE TRANSPORTE'                        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.19.0004','AUXILIOS DIVERSOS'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.19.0005','CONTRIBUICOES SINDICAIS'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.21.0001','VALE REFEICAO'                          ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('3.03.03.21.0002','CESTA BASICA'                           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0001','ALUGUEL'                                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0002','CONDOMINIO'                             ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0003','IPTU'                                   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0004','ENERGIA ELETRICA'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0005','AGUA'                                   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0006','MANUTENCAO PREDIAL'                     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0007','TELEFONIA E COMUNICACAO'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0008','OUTRAS DESPESAS COM OCUPACAO'           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0009','MATERIAL DE ESCRITORIO'                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0010','MATERIAL DE INFORMATICA'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0011','IMPRESSOS DIVERSOS'                     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0012','MATERIAIS DIVERSOS'                     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0013','ASSESSORIA JURIDICA'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0014','ASSESSORIA CONTABIL'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0015','ASSESSORIA DE INFORMATICA'              ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0016','ASSESSORIA FINANCEIRA'                  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0017','ASSESSORIA DE DEPTO PESSOAL'            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0018','ASSESSORIAS DIVERSAS'                   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0019','SERVICOS DE MOTOBOY E FRETES'           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0020','MANUTENCAO DE EQUIPAMENTOS'             ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0021','SERVICOS DE TERCEIROS DIVERSOS'         ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0022','HOSTING / SERVIDORES'                   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0023','EMAILS EXTERNOS / CLOUND'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0024','FERRAMENTAS DE BUSCA WEB'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0025','ANUNCIOS E MARKETING PERIODICOS'        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0026','ANUNCIOS E MARKETING WEB'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0027','ANUNCIOS E MARKETING MIDIA'             ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0028','ASSESSORIA DE IMPRENSA'                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0029','REGISTRO DE MARCAS/PATENTES'            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0030','PIS RETIDO DE TERCEIROS'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0031','COFINS RETIDO DE TERCEIROS'             ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0032','CSLL RETIDO DE TERCEIROS'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0033','IRRF RETIDO DE TERCEIROS'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0034','ISS RETIDO DE TERCEIROS'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0035','INSS RETIDO DE TERCEIROS'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0036','ACOES JUDICIAIS FISCAIS'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0037','ACOES JUDICIAIS CIVEIS'                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0038','DESPESA COM EVENTOS'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0039','DESPESA COM VIAGENS'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0040','DESPESAS COM PROPAGANDA'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0041','DESPESAS COM MARCAS E PATENTES'         ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0042','BRINDES E DOACOES'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0043','ASSINATURAS DE PERIODICOS'              ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0044','COMBUSTIVEL'                            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0045','PEDAGIOS'                               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0046','DESPESA COM BENS DE PEQ VALOR'          ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0047','DESPESAS COM CARTORIOS'                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0048','DESPESAS COM VEICULOS'                  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0049','COPA E COZINHA'                         ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0050','DESPESAS ADM DIVERSAS'                  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0051','LOCACAO DE EQUIPAMENTOS'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0052','LOCACAO DE VEICULOS'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0053','LEASING DE VEICULOS'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0054','LEASING DE EQUIPAMENTOS'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0055','CPMF'                                   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0056','TARIFAS BANCARIAS'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0057','OUTRAS DESPESAS BANCARIAS'              ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0058','OUTRAS RECEITAS BANCARIAS'              ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0059','VARIACAO CAMBIAL ATIVA'                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0060','VARIACAO CAMBIAL PASSIVA'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0061','DESP. CAMBIAIS DIVERSAS'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0062','JUROS RECEBIDOS APLICACOES FINANCEIRAS' ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0063','JUROS RECEBIDOS EMPRESTIMOS TERCEIROS'  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0064','JUROS RECEBIDOS CONTRATOS DE MUTUO'     ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0065','JUROS RECEBIDOS OUTROS'                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0066','JUROS INCORRIDOS CAPITAL DE GIRO'       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0067','JUROS INCORRIDOS EMPREST LONGO PRAZO'   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0068','JUROS INCORRIDOS CONTA GARANTIDA'       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0069','JUROS INCORRIDOS EMPRESTIMO TERCEIROS'  ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0070','JUROS INCORRIDOS CONTRATOS DE MUTUO'    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0071','JUROS INCORRIDOS CAPITAL PROPRIO'       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0072','JUROS INCORRIDOS OUTROS'                ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0073','DESP. FINANCEIRAS DIVERSAS'             ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0074','OPERADORAS CARTOES DE CREDITO'          ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0075','OPERADORAS CARTOES DE DEBITO'           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0076','OPERADORAS PAYPAL'                      ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('4.01.01.01.0077','EMISSAO E CUSTODIA DE BOLETOS'          ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.01.0001','RECEITA DE SERVICOS'                    ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.02.0002','OUTRAS RECEITAS'                        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.02.0003','COMISSOES POR VENDAS / INTERMEDIACAO'   ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0001','PIS APURACAO'                           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0002','COFINS APURACAO'                        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0003','CSLL APURACAO'                          ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0004','IMPOSTO DE RENDA'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0005','ISS APURACAO'                           ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0006','INSS APURACAO (FATURAMENTO)'            ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0007','SIMPLES APURACAO'                       ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0008','COMISSOES SOBRE VENDAS'                 ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0009','COMISSOES PORTAL DE VENDAS WEB'         ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0010','COMISSOES REPRESENTANTES'               ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0011','CANCELAMENTO DE VENDAS/SERVICOS'        ,'S','0.00.0000','S','S',1);
INSERT INTO VCONTACONTABIL(CC_CODIGO,CC_NOME,CC_LANCTO,CC_CODCTR,CC_ATIVO,CC_REG,CC_CODUSR) VALUES('5.01.01.03.0012','DEVOLUCAO DE VENDAS'                    ,'S','0.00.0000','S','S',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- CC_CODIGO      | PK  |    |    | VC(15) NN          |
   -- CC_NOME        |     |    |    | VC(40) NN          |
   -- CC_LANCTO      | CC  |    |    | VC(1) NN           |  
   -- CC_CODCTR      | SEL |    |    | VC(09) NN          | Campo relacionado (CONTARESUMO)     
   -- CTR_NOME       | SEL |    |    | VC(40) NN          | Campo relacionado (CONTARESUMO)        
   -- CC_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CC_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CC_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D13         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                              C O N T A R E S U M O                              
--tblcontaresumo
-------------------------------------------------------------------------------------
GO
CREATE TABLE CONTARESUMO(
  CTR_CODIGO VARCHAR(9) PRIMARY KEY NOT NULL
  ,CTR_NOME VARCHAR(40) NOT NULL
  ,CTR_ATIVO VARCHAR(1) NOT NULL
  ,CTR_REG VARCHAR(1) NOT NULL
  ,CTR_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CtrCodigo CHECK( CRT_CODIGO LIKE('[0-9][.][0-9][0-9][.][0-9][0-9][0-9][0-9]'))    
  ,CONSTRAINT chk_CtrAtivo CHECK( CTR_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CtrReg CHECK( CTR_REG IN('A','P','S')));
GO
CREATE VIEW VCONTARESUMO AS
  SELECT CTR_CODIGO,CTR_NOME,CTR_ATIVO,CTR_REG,CTR_CODUSR FROM CONTARESUMO
GO  
INSERT INTO dbo.VCONTARESUMO(CTR_CODIGO,CTR_NOME,CTR_ATIVO,CTR_REG,CTR_CODUSR) VALUES('0.00.0000','NAO INFORMADO' ,'S'  ,'S'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- CTR_CODIGO     | PK  |    |    | VC(9) NN           |
   -- CTR_NOME       |     |    |    | VC(40) NN          |
   -- CTR_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CTR_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CTR_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D13         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                     C F O P                                     --
--tblcfop
-------------------------------------------------------------------------------------
CREATE TABLE CFOP(
  CFO_CODIGO VARCHAR(5) PRIMARY KEY NOT NULL
  ,CFO_NOME VARCHAR(60) NOT NULL
  ,CFO_ENTSAI VARCHAR(1) NOT NULL
  ,CFO_RELCOMPRA VARCHAR(1) NOT NULL
  ,CFO_RELVENDA VARCHAR(1) NOT NULL
  ,CFO_ATIVO VARCHAR(1) NOT NULL
  ,CFO_REG VARCHAR(1) NOT NULL
  ,CFO_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CfoCodigo CHECK( CFO_CODIGO LIKE('[0-9][.][0-9][0-9][0-9]'))  
  ,CONSTRAINT chk_CfoEntSai CHECK( CFO_ENTSAI IN('E','S'))  
  ,CONSTRAINT chk_CfoRelCompra CHECK( CFO_RELCOMPRA IN('S','N'))  
  ,CONSTRAINT chk_CfoRelVenda CHECK( CFO_RELVENDA IN('S','N'))  
  ,CONSTRAINT chk_CfoAtivo CHECK( CFO_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CfoReg CHECK( CFO_REG IN('A','P','S')));
GO
CREATE VIEW VCFOP AS
  SELECT CFO_CODIGO,CFO_NOME,CFO_RELCOMPRA,CFO_RELVENDA,CFO_ATIVO,CFO_REG,CFO_CODUSR FROM CFOP
GO  
INSERT INTO CFOP(CFO_CODIGO,CFO_NOME,CFO_RELCOMPRA,CFO_RELVENDA,CFO_ATIVO,CFO_REG,CFO_CODUSR) VALUES('5.102','VENDA DE MERC.ADQ.DE TERC.DENTRO ESTADO','S','S','S','P',1);
INSERT INTO CFOP(CFO_CODIGO,CFO_NOME,CFO_RELCOMPRA,CFO_RELVENDA,CFO_ATIVO,CFO_REG,CFO_CODUSR) VALUES('6.102','VENDA DE MERC.ADQ.DE TERC.DENTRO ESTADO','S','S','S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- CFO_CODIGO     | PK  |    |    | VC(05) NN          |
   -- CFO_NOME       |     |    |    | VC(60) NN          |
   -- CFO_ENTSAI     | CC  |    |    | VC(01) NN          |  
   -- CFO_RELCOMPRA  | CC  |    |    | VC(01) NN          |   
   -- CFO_RELVENDA   | CC  |    |    | VC(01) NN          |      
   -- CFO_ATIVO      | CC  |    |    | VC(01) NN          | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CFO_REG        | FNC |    |    | VC(01) NN          | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CFO_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(01) NN          | Retornar se o usuario eh PUB/ADM
   -- UP_D14         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                          C N A B A R Q U I V O                                  --
--tblcnabarquivo
-------------------------------------------------------------------------------------
GO
CREATE TABLE CNABARQUIVO(
  ARQ_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,ARQ_CODLAY INTEGER NOT NULL
  ,ARQ_DTARQUIVO DATE NOT NULL
  ,ARQ_DTGERACAO TIMESTAMP
  ,ARQ_AGENDADO DATE
  ,ARQ_NOME VARCHAR(60) NOT NULL
  ,ARQ_VALOR NUMERIC(15,2) NOT NULL
  --,ARQ_OBS BLOB SUB_TYPE TEXT SEGMENT SIZE 80  --nao entra na view
  ,ARQ_ATIVO VARCHAR(1) NOT NULL
  ,ARQ_REG VARCHAR(1) NOT NULL
  ,ARQ_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_ArqAtivo CHECK( ARQ_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_ArqReg CHECK( ARQ_REG IN('A','P','S')));
GO
CREATE VIEW VCNABARQUIVO AS
  SELECT ARQ_CODIGO
         ,ARQ_CODLAY
         ,ARQ_DTARQUIVO
         ,ARQ_DTGERACAO
         ,ARQ_AGENDADO
         ,ARQ_NOME
         ,ARQ_VALOR
         ,ARQ_ATIVO
         ,ARQ_REG
         ,ARQ_CODUSR
    FROM CNABARQUIVO
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- ARQ_CODIGO     | PK  |    |    | INT                |  Auto incremento
   -- ARQ_CODLAY     |     |    |    | INT NN             |
   -- ARQ_DTARQUIVO  |     |    |    | DAT NN             |
   -- ARQ_DTGERACAO  |     |    |    | DAT NN             |
   -- ARQ_AGENDADO   |     |    |    | DAT                |
   -- ARQ_NOME       |     |    |    | VC(60) NN          |
   -- ARQ_VALOR      |     |    |    | NUM(15,2) NN       |
   -- ARQ_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- ARQ_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- ARQ_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D10         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                          C N A B E N V I O                                      --
--tblcnabenvio
-------------------------------------------------------------------------------------
CREATE TABLE CNABENVIO(
  ENV_GUIA INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,ENV_CODARQ INTEGER NOT NULL
  ,ENV_CODLAY INTEGER NOT NULL
  ,ENV_CODBARRAS VARCHAR(60)
  ,ENV_NOSSONUMERO VARCHAR(20)
  ,ENV_CODCI1 VARCHAR(6) NOT NULL
  ,ENV_CODCI2 VARCHAR(6) NOT NULL
  ,ENV_CODCR VARCHAR(6) NOT NULL
  ,ENV_MSG01 VARCHAR(50)
  ,ENV_MSG02 VARCHAR(50)
  ,ENV_MSG03 VARCHAR(50)
  ,ENV_MSG04 VARCHAR(50)
  ,ENV_ABATIMENTO NUMERIC(15,2) NOT NULL
  ,ENV_JUROS NUMERIC(15,2) NOT NULL
  ,ENV_MULTA NUMERIC(15,2) NOT NULL
  ,ENV_DIASPROTESTO INTEGER NOT NULL
  ,ENV_DATAPAGA DATE  
  ,ENV_ATIVO VARCHAR(1) NOT NULL
  ,ENV_REG VARCHAR(1) NOT NULL
  ,ENV_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_EnvAtivo CHECK( ENV_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_EnvReg CHECK( ENV_REG IN('A','P','S')));  
GO
CREATE VIEW VCNABENVIO AS
  SELECT ENV_GUIA
         ,ENV_CODARQ
         ,ENV_CODLAY
         ,ENV_CODBARRAS
         ,ENV_NOSSONUMERO
         ,ENV_CODCI1
         ,ENV_CODCI2
         ,ENV_CODCR
         ,ENV_MSG01
         ,ENV_MSG02
         ,ENV_MSG03
         ,ENV_MSG04
         ,ENV_ABATIMENTO
         ,ENV_JUROS
         ,ENV_MULTA
         ,ENV_DIASPROTESTO
         ,ENV_DATAPAGA
         ,ENV_ATIVO
         ,ENV_REG
         ,ENV_CODUSR
    FROM CNABENVIO
   -- -----------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO            |INS  |UPD |DEL | TIPO               | Obs
   -- -----------------|-----|----|----|--------------------|----------------------------------------------------------
   -- ENV_CODIGO       | PK  |    |    | INT                |  Auto incremento
   -- ENV_CODARQ       |     |    |    | INT NN             |
   -- ENV_CODLAY       |     |    |    | INT NN             |
   -- ENV_CODBARRAS    |     |    |    | VC(60)             |
   -- ENV_NOSSONUMERO  |     |    |    | VC(20)             |
   -- ENV_CODCI1       |     |    |    | VC(6) NN           |
   -- ENV_CODCI2       |     |    |    | VC(6) NN           |
   -- ENV_CODCR        |     |    |    | VC(6) NN           |
   -- ENV_MSG01        |     |    |    | VC(50)             |
   -- ENV_MSG02        |     |    |    | VC(50)             |
   -- ENV_MSG03        |     |    |    | VC(50)             |
   -- ENV_MSG04        |     |    |    | VC(50)             |
   -- ENV_ABATIMENTO   |     |    |    | NUM(15,2) NN       |
   -- ENV_JUROS        |     |    |    | NUM(15,2) NN       |
   -- ENV_MULTA        |     |    |    | NUM(15,2) NN       |
   -- ENV_DIASPROTESTO |     |    |    | INT NN             |
   -- ENV_DATAPAGA     |     |    |    | DATE               |
   -- ENV_ATIVO        | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- ENV_REG          | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- ENV_CODUSR       | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO      | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB       | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D10           | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31           | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- -----------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                          C N A B E R R O                                        --
--tblcnaberro
-------------------------------------------------------------------------------------
CREATE TABLE CNABERRO(
  ERR_CODBNC INTEGER NOT NULL
  ,ERR_CODPTP VARCHAR(2) NOT NULL       --PAGARTIPO
  ,ERR_CODIGO VARCHAR(10) NOT NULL
  ,ERR_DESCRICAO VARCHAR(100) NOT NULL
  ,ERR_ACAO VARCHAR(8) NOT NULL
  ,ERR_ATIVO VARCHAR(1) NOT NULL
  ,ERR_REG VARCHAR(1) NOT NULL
  ,ERR_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_ErrAtivo CHECK( ERR_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_ErrReg CHECK( ERR_REG IN('A','P','S'))  
  ,CONSTRAINT PKCNABERRO PRIMARY KEY (ERR_CODBNC, ERR_CODPTP, ERR_CODIGO));
GO
CREATE VIEW VCNABERRO AS
  SELECT ERR_CODBNC
         ,ERR_CODPTP
         ,ERR_CODIGO
         ,ERR_DESCRICAO
         ,ERR_ACAO
         ,ERR_ATIVO
         ,ERR_REG
         ,ERR_CODUSR
    FROM CNABERRO
GO    
CREATE TABLE dbo.BKPAGENDATAREFA(
  ERR_ID INTEGER IDENTITY PRIMARY KEY NOT NULL 
  ,ERR_ACAO VARCHAR(1) NOT NULL
  ,ERR_DATA DATE DEFAULT GETDATE() NOT NULL
  ,ERR_CODBNC INTEGER NOT NULL
  ,ERR_CODPTP VARCHAR(2) NOT NULL
  ,ERR_CODIGO VARCHAR(10) NOT NULL
  ,ERR_DESCRICAO VARCHAR(100) NOT NULL
  ,ERR_ACAO VARCHAR(8) NOT NULL
  ,ERR_ATIVO VARCHAR(1) NOT NULL
  ,ERR_REG VARCHAR(1) NOT NULL
  ,ERR_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_bkpErrAcao CHECK( ERR_ACAO IN('I','A','E'))  
);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- ERR_CODBNC     | SEL |    |    | INT NN             | Campo relacionado (BANCO)
   -- BNC_NOME       | SEL |    |    | VC(40) NN          | Campo relacionado (BANCO)   
   -- ERR_CODPTP     | SEL |    |    | VC(15) NN          | Campo relacionado (PAGARTIPO)
   -- PTP_NOME       | SEL |    |    | VC(25) NN          | Campo relacionado (PAGARTIPO)   
   -- ERR_CODIGO     |     |    |    | VC(10) NN
   -- ERR_DESCRICAO  |     |    |    | VC(100) NN
   -- ERR_ACAO       |     |    |    | VC(8) NN
   -- ERR_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- ERR_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- ERR_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D30         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                          C N A B I N S T R U C A O                              --
--tblcnabinstrucai
-------------------------------------------------------------------------------------
GO
CREATE TABLE CNABINSTRUCAO(
  CI_CODIGO VARCHAR(6) NOT NULL
  ,CI_CODBCD VARCHAR(6) NOT NULL             --BANCOCODIGO
  ,CI_NOME VARCHAR(30) NOT NULL
  ,CI_ATIVO VARCHAR(1) NOT NULL
  ,CI_REG VARCHAR(1) NOT NULL
  ,CI_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CiAtivo CHECK( CI_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CiReg CHECK( CI_REG IN('A','P','S'))  
  ,CONSTRAINT PKTABCNABINSTRUCAO PRIMARY KEY (CI_CODIGO, CI_CODBCD));
GO
CREATE VIEW VCNABINSTRUCAO AS
  SELECT CI_CODIGO
         ,CI_CODBCD
         ,CI_NOME
         ,CI_ATIVO
         ,CI_REG
         ,CI_CODUSR
    FROM CNABINSTRUCAO
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('00','341','NAO NECESSARIO'     ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('00','237','NAO NECESSARIO'     ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('01','341','INCLUIR'            ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('06','341','ALT DE VENCIMENTO'  ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('34','341','BAIXA MANUAL'       ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('09','341','PROTESTAR'          ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('01','237','INCLUIR'            ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('02','237','PEDIDO DE BAIXA'    ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('04','237','ABATIMENTO'         ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('06','237','ALT DE VENCIMENTO'  ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('07','237','CONCESSAO DE DESC'  ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('09','237','PROTESTAR'          ,'S','P',1);
INSERT INTO CNABINSTRUCAO(CI_CODIGO,CI_CODBCD,CI_NOME,CI_ATIVO,CI_REG,CI_CODUSR) VALUES('03','341','ENTRADA REJEITADA'  ,'S','P',1);
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS   |UPD |DEL | TIPO               | Obs
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------
   -- CI_CODIGO      | PK   |    |    | VC(6) NN           | Campo informado
   -- CI_CODBCD      |PK/SEL|    |    | VC(6) NN           | Campo relacionado (BANCOCODIGO)      
   -- BCD_NOME       | SEL  |    |    | VC(20) NN          | Campo relacionado (BANCOCODIGO)         
   -- CI_NOME        |      |    |    | VC(30) NN          |
   -- CI_ATIVO       | CC   |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CI_REG         | FNC  |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CI_CODUSR      | OK   |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL  |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL  |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D30         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ----------------------------------------------------------------------------------------------------------------
--
--
-------------------------------------------------------------------------------------
--                          C N A B L A Y O U T                                    --
--tblcnablayout
-------------------------------------------------------------------------------------
GO
CREATE TABLE CNABLAYOUT(
  LAY_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,LAY_CODBNC INTEGER NOT NULL
  ,LAY_DESCRICAO VARCHAR(30) NOT NULL
  ,LAY_CARTEIRA VARCHAR(3)---FALTOUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUU
  ,LAY_CONVENIO VARCHAR(20) NOT NULL
  ,LAY_BOLETO VARCHAR(1) NOT NULL
  ,LAY_MSG01 VARCHAR(50)
  ,LAY_MSG02 VARCHAR(50)
  ,LAY_MSG03 VARCHAR(50)
  ,LAY_MSG04 VARCHAR(50)
  ,LAY_CODPTP VARCHAR(2) NOT NULL       --PAGARTIPO
  ,LAY_PCMULTA NUMERIC(15,4) NOT NULL
  ,LAY_INST01 VARCHAR(4)
  ,LAY_INST02 VARCHAR(4)
  ,LAY_PROTESTAR INTEGER NOT NULL
  ,LAY_DTLIMITEDESCONTO INTEGER NOT NULL
  ,LAY_PCDESCONTO NUMERIC(15,4) NOT NULL
  ,LAY_ATIVO VARCHAR(1) NOT NULL
  ,LAY_REG VARCHAR(1) NOT NULL
  ,LAY_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_LayBoleto CHECK( LAY_BOLETO IN('B','C'))  
  ,CONSTRAINT chk_LayAtivo CHECK( LAY_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_LayReg CHECK( LAY_REG IN('A','P','S')));
GO
CREATE VIEW VCNABLAYOUT AS
  SELECT LAY_ID
         ,LAY_CODBNC
         ,LAY_DESCRICAO
         ,LAY_CARTEIRA
         ,LAY_CONVENIO
         ,LAY_BOLETO
         ,LAY_MSG01
         ,LAY_MSG02
         ,LAY_MSG03
         ,LAY_MSG04
         ,LAY_CODPTP
         ,LAY_PCMULTA
         ,LAY_INST01
         ,LAY_INST02
         ,LAY_PROTESTAR
         ,LAY_DTLIMITEDESCONTO
         ,LAY_PCDESCONTO
         ,LAY_ATIVO
         ,LAY_REG
         ,LAY_CODUSR
    FROM CNABLAYOUT
   -- ---------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO                |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------------|-----|----|----|--------------------|----------------------------------------------------------
   -- LAY_CODIGO           | PK  |    |    | INT                |  Auto incremento
   -- PGR_CODBNC           | SEL |    |    | INT NN             | Campo relacionado (BANCO)
   -- BNC_NOME             | SEL |    |    | VC(40) NN          | Campo relacionado (BANCO)   
   -- LAY_DESCRICAO        |     |    |    | VC(30) NN          |   
   -- LAY_CARTEIRA         |     |    |    | VC(3) NN           | FALTOUUUUUUUUUUUUUU   
   -- LAY_CONVENIO         |     |    |    | VC(20) NN          |  
   -- LAY_BOLETO           | CC  |    |    | VC(1) NN           | Quem emite Banco ou Cliente
   -- LAY_MSG01            |     |    |    | VC(50)             |
   -- LAY_MSG02            |     |    |    | VC(50)             |
   -- LAY_MSG03            |     |    |    | VC(50)             |
   -- LAY_MSG04            |     |    |    | VC(50)             |
   -- LAY_CODPTP           | SEL |    |    | VC(15) NN          | Campo relacionado (PAGARTIPO)
   -- PTP_NOME             | SEL |    |    | VC(25) NN          | Campo relacionado (PAGARTIPO)   
   -- LAY_PCMULTA          |     |    |    | NUM(15,4) NN       |
   -- LAY_INST01           |     |    |    | VC(4)              |
   -- LAY_INST02           |     |    |    | VC(4)              |
   -- LAY_PROTESTAR        |     |    |    | INT NN             |
   -- LAY_DTLIMITEDESCONTO |     |    |    | INT NN             |
   -- LAY_PCDESCONTO       |     |    |    | NUM(15,4) NN       |
   -- LAY_ATIVO            | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- LAY_REG              | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- LAY_CODUSR           | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO          | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB           | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D30               | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31               | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ----------------------------------------------------------------------------------------------------------------------
--
--    
-------------------------------------------------------------------------------------
--                          C N A B R E T O R N O                                  --
--tblretorno
-------------------------------------------------------------------------------------
GO
CREATE TABLE CNABRETORNO(
  CR_CODIGO VARCHAR(6) NOT NULL
  ,CR_CODBCD VARCHAR(6) NOT NULL             --BANCOCODIGO
  ,CR_NOME VARCHAR(30) NOT NULL
  ,CR_TIPO VARCHAR(1) NOT NULL
  ,CR_ATIVO VARCHAR(1) NOT NULL
  ,CR_REG VARCHAR(1) NOT NULL
  ,CR_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CrTipo CHECK( CR_TIPO IN('A','B','C','D','E','F','G'))  
  ,CONSTRAINT chk_CrAtivo CHECK( CR_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CrReg CHECK( CR_REG IN('A','P','S'))
  ,CONSTRAINT PKCNABRETORNO PRIMARY KEY (CR_CODIGO, CR_CODBCD));
GO
CREATE VIEW VCNABRETORNO AS
  SELECT CR_CODIGO
         ,CR_CODBCD
         ,CR_NOME
         ,CR_TIPO
         ,CR_ATIVO
         ,CR_REG
         ,CR_CODUSR
  FROM CNABRETORNO
GO
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('02'  ,'341'  ,'ENTRADA CONFIRMADA'             ,'A'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('03'  ,'341'  ,'ENTRADA REJEITADA'              ,'C'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('04'  ,'341'  ,'ALTERACAO DE DADOS ENTRADA'     ,'A'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('05'  ,'341'  ,'ALTERACAO DE DADOS BAIXA'       ,'A'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('06'  ,'341'  ,'LIQUIDACAO NORMAL'              ,'B'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('07'  ,'341'  ,'LIQUIDACAO PARCIAL'             ,'B'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('08'  ,'341'  ,'LIQUIDACAO EM CARTORIO'         ,'B'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('09'  ,'341'  ,'BAIXA SIMPLES'                  ,'B'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('10'  ,'341'  ,'BAIXA POR TER SIDO LIQUIDADO'   ,'B'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('11'  ,'341'  ,'EM SER (SO NO RETORNO MENSAL)'  ,'B'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('12'  ,'341'  ,'ABATIMENTO CONCEDIDO'           ,'A'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('13'  ,'341'  ,'ABATIMENTO CANCELADO'           ,'C'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('14'  ,'341'  ,'VENCIMENTO ALTERADO'            ,'A'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('15'  ,'341'  ,'BAIXAS REJEITADAS'              ,'C'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('23'  ,'341'  ,'TARIFA ENVIO CATORIO'           ,'A'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('24'  ,'341'  ,'PROTESTO REJEITADO'             ,'C'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('32'  ,'341'  ,'BAIXA POR PROTESTO'             ,'B'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('47'  ,'341'  ,'BAIXA PARA DESCONTO'            ,'A'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('59'  ,'341'  ,'BAIXA POR CC SISPAG'            ,'B'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('60'  ,'341'  ,'ENTRADA REJEITADA CARNE'        ,'C'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('72'  ,'341'  ,'BAIXA POR CC'                   ,'B'  ,'S','P',1);
INSERT INTO CNABRETORNO(CR_CODIGO,CR_CODBCD,CR_NOME,CR_TIPO,CR_ATIVO,CR_REG,CR_CODUSR) VALUES('73'  ,'341'  ,'COBRANCA SIMPLES'               ,'C'  ,'S','P',1);
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS   |UPD |DEL | TIPO               | Obs
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------
   -- CR_CODIGO      | PK   |    |    | VC(6) NN           | Campo informado
   -- CR_CODBCD      |PK/SEL|    |    | VC(6) NN           | Campo relacionado (BANCOCODIGO)      
   -- BCD_NOME       | SEL  |    |    | VC(20) NN          | Campo relacionado (BANCOCODIGO)         
   -- CR_NOME        |      |    |    | VC(30) NN          |
   -- CR_TIPO        | CC   |    |    | VC(1) NN           |  
   -- CR_ATIVO       | CC   |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CR_REG         | FNC  |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CR_CODUSR      | OK   |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL  |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL  |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D30         | SEL  |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL  |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             C O M P E T E N C I A                               --
-- Tabela multi-empresa                                                            --
-- Campo ativo informa se aceita lancamento
--tblcompetencia
-------------------------------------------------------------------------------------
CREATE TABLE COMPETENCIA(
  CMP_CODIGO INTEGER NOT NULL
  ,CMP_CODEMP INTEGER NOT NULL
  ,CMP_NOME VARCHAR(6) NOT NULL
  ,CMP_EMFECHAMENTO VARCHAR(1) NOT NULL
  ,CMP_ATIVO VARCHAR(1) NOT NULL
  ,CMP_REG VARCHAR(1) NOT NULL
  ,CMP_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CmpNome CHECK( CMP_NOME LIKE('[A-Z][A-Z][A-Z][/][0-9][0-9]'))    
  ,CONSTRAINT chk_CmpEmFechamento CHECK( CMP_EMFECHAMENTO IN('S','N'))      
  ,CONSTRAINT chk_CmpAtivo CHECK( CMP_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CmpReg CHECK( CMP_REG IN('A','P','S'))
  ,CONSTRAINT PKCOMPETENCIA PRIMARY KEY(CMP_CODIGO,CMP_CODEMP));    
GO
CREATE VIEW VCOMPETENCIA AS
  SELECT CMP_CODIGO
         ,CMP_CODEMP
         ,CMP_NOME
         ,CMP_EMFECHAMENTO
         ,CMP_ATIVO
         ,CMP_REG
         ,CMP_CODUSR
  FROM COMPETENCIA
GO
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201801,1,'JAN/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201802,1,'FEV/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201803,1,'MAR/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201804,1,'ABR/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201805,1,'MAI/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201806,1,'JUN/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201807,1,'JUL/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201808,1,'AGO/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201809,1,'SET/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201810,1,'OUT/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201811,1,'NOV/18','N','S','P',1);
INSERT INTO COMPETENCIA(CMP_CODIGO,CMP_CODEMP,CMP_NOME,CMP_EMFECHAMENTO,CMP_ATIVO,CMP_REG,CMP_CODUSR) VALUES(201812,1,'DEZ/18','N','S','P',1);
   -- -----------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO            |INS  |UPD |DEL | TIPO               | Obs
   -- -----------------|-----|----|----|--------------------|----------------------------------------------------------
   -- CMP_CODIGO       | PK  |    |    | INT                | Campo informado 
   -- CMP_CODEMP       | PK  |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO      | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- CMP_NOME         |     |    |    | VC(6) NN           | Campo gerado pelo trigger JAN/18,FEV/18
   -- CMP_EMFECHAMENTO | CC  |    |    | VC(1) NN           |  
   -- CMP_ATIVO        | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CMP_REG          | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CMP_CODUSR       | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO      | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB       | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D07           | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31           | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- -----------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                C O N T A D O R                                  --
--tblcontador
-------------------------------------------------------------------------------------
CREATE TABLE CONTADOR(
  CNT_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL 
  ,CNT_CODEMP INTEGER NOT NULL
  ,CNT_CRC VARCHAR(15) NOT NULL
  ,CNT_CPF VARCHAR(14) NOT NULL
  ,CNT_CODQUALIF VARCHAR(10)
  ,CNT_CODCDD VARCHAR(7) NOT NULL           --CIDADE
  ,CNT_CNPJ VARCHAR(14) NOT NULL
  ,CNT_NOME VARCHAR(60) NOT NULL
  ,CNT_CEP VARCHAR(8) NOT NULL
  ,CNT_CODLGR VARCHAR(5) NOT NULL                 --LOGRADOURO
  ,CNT_ENDERECO VARCHAR(60) NOT NULL
  ,CNT_NUMERO VARCHAR(10) NOT NULL
  ,CNT_FONE VARCHAR(10)
  ,CNT_EMAIL VARCHAR(60)
  ,CNT_BAIRRO VARCHAR(15) NOT NULL
  ,CNT_SUFRAMA VARCHAR(9)
  ,CNT_CODINCTRIB VARCHAR(1) NOT NULL
  ,CNT_INDAPROCRED VARCHAR(1) NOT NULL
  ,CNT_CODTIPOCONT VARCHAR(1) NOT NULL
  ,CNT_INDREGCUM VARCHAR(1) NOT NULL
  ,CNT_CODRECPIS VARCHAR(6)
  ,CNT_CODRECCOFINS VARCHAR(6)
  ,CNT_INDNATPJ VARCHAR(2)
  ,CNT_INDATIV VARCHAR(1)
  ,CNT_ATIVO VARCHAR(1) NOT NULL
  ,CNT_REG VARCHAR(1) NOT NULL
  ,CNT_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CntCep CHECK( CNT_CEP LIKE('[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]'))      
  ,CONSTRAINT chk_CntCpf CHECK( CNT_CPF NOT LIKE '%[^0-9]%' )      
  ,CONSTRAINT chk_CntCnpj CHECK( CNT_CNPJ NOT LIKE '%[^0-9]%' )        
  ,CONSTRAINT chk_CntAtivo CHECK( CNT_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CntReg CHECK( CNT_REG IN('A','P','S')));
GO  
CREATE VIEW VCONTADOR AS
  SELECT CNT_CODIGO
         ,CNT_CODEMP
         ,CNT_CRC
         ,CNT_CPF
         ,CNT_CODQUALIF
         ,CNT_CODCDD
         ,CNT_CNPJ
         ,CNT_NOME
         ,CNT_CEP
         ,CNT_CODLGR
         ,CNT_ENDERECO
         ,CNT_NUMERO
         ,CNT_FONE
         ,CNT_EMAIL
         ,CNT_BAIRRO
         ,CNT_SUFRAMA
         ,CNT_CODINCTRIB
         ,CNT_INDAPROCRED
         ,CNT_CODTIPOCONT
         ,CNT_INDREGCUM
         ,CNT_CODRECPIS
         ,CNT_CODRECCOFINS
         ,CNT_INDNATPJ
         ,CNT_INDATIV
         ,CNT_ATIVO
         ,CNT_REG
         ,CNT_CODUSR
    FROM CONTADOR
   -- -----------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO            |INS  |UPD |DEL | TIPO               | Obs
   -- -----------------|-----|----|----|--------------------|----------------------------------------------------------
   -- CNT_CODIGO       | PK  |    |    | INT                |  Auto incremento
   -- CNT_CRC          |     |    |    | VC(15) NN          |   
   -- CNT_CPF          |     |    |    | VC(14) NN          |      
   -- CNT_CODQC        | SEL |    |    | VC(4) NN           | Campo relacionado (QUALIFICACAOCONT)   
   -- QC_NOME          | SEL |    |    | VC(70) NN          | Campo relacionado (QUALIFICACAOCONT)      
   -- CNT_CODCDD       | SEL |    |    | VC(7) NN           | Campo relacionado (CIDADE)   
   -- CDD_NOME         | SEL |    |    | VC(30) NN          | Campo relacionado (CIDADE)      
   -- CNT_CNPJ         |     |    |    | VC(14) NN          |      
   -- CNT_NOME         |     |    |    | VC(60) NN          |
   -- CNT_CODLGR       | SEL |    |    | VC(5) NN           | Campo relacionado (LOGRADOURO)   
   -- LGR_NOME         | SEL |    |    | VC(20) NN          | Campo relacionado (LOGRADOURO)      
   -- CNT_ENDERECO     |     |    |    | VC(60) NN          |
   -- CNT_NUMERO       |     |    |    | VC(10) NN          |   
   -- CNT_CEP          |     |    |    | VC(8) NN           |
   -- CNT_BAIRRO       |     |    |    | VC(15) NN          |   
   -- CNT_FONE         |     |    |    | VC(10) NN          |
   -- CNT_EMAIL        |     |    |    | VC(60)             |
   -- CNT_SUFRAMA      |     |    |    | VC(9)              |
   -- CNT_CODINCTRIB   |     |    |    | VC(1) NN           |
   -- CNT_INDAPROCRED  |     |    |    | VC(1) NN           |
   -- CNT_CODTIPOCONT  |     |    |    | VC(1) NN           |
   -- CNT_INDREGCUM    |     |    |    | VC(1) NN           |
   -- CNT_CODRECPIS    |     |    |    | VC(6)              |
   -- CNT_CODRECCOFINS |     |    |    | VC(6)              |
   -- CNT_INDNATPJ     |     |    |    | VC(2)              |
   -- CNT_INDATIV      |     |    |    | VC(1)              |
   -- CNT_CODEMP       | SEL |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO      | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- CNT_ATIVO        | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CNT_REG          | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CNT_CODUSR       | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO      | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB       | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D03           | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31           | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- -----------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                                C O N T R A T O                                  --
--tblcontrato
-------------------------------------------------------------------------------------
CREATE TABLE CONTRATO(
  CTT_CODIGO INTEGER PRIMARY KEY NOT NULL
  ,CTT_CODEMP INTEGER NOT NULL    --EMPRESA
  ,CTT_CODFVR INTEGER NOT NULL    --FAVORECIDO
  ,CTT_CODFC INTEGER NOT NULL     --FORMACOBRANCA
  ,CTT_EMISSAO DATE NOT NULL
  ,CTT_VALOR NUMERIC(15,2) NOT NULL
  ,CTT_FATURADO NUMERIC(15,2) NOT NULL
  ,CTT_NUMERO VARCHAR(10) NOT NULL
  ,CTT_ATIVO VARCHAR(1) NOT NULL
  ,CTT_REG VARCHAR(1) NOT NULL
  ,CTT_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_CttAtivo CHECK( CTT_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_CttReg CHECK( CTT_REG IN('A','P','S')));
GO
CREATE VIEW VCONTRATO AS
  SELECT CTT_CODIGO
         ,CTT_CODEMP
         ,CTT_CODFVR
         ,CTT_CODFC
         ,CTT_EMISSAO
         ,CTT_VALOR
         ,CTT_FATURADO
         ,CTT_NUMERO
         ,CTT_ATIVO
         ,CTT_REG
         ,CTT_CODUSR
     FROM CONTRATO
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- CTT_CODIGO     | PK  |    |    | INT                | Gerado pela aplicacao
   -- CTT_CODEMP     | PK  |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- CTT_CODFVR     | SEL |    |    | INT NN             | Campo relacionado (FAVORECIDO)
   -- FVR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (FAVORECIDO)   
   -- CTT_CODFC      | SEL |    |    | VC(3) NN           | Campo relacionado (FORMACOBRANCA)   
   -- FC_NOME        | SEL |    |    | VC(20) NN          | Campo relacionado (FORMACOBRANCA)      
   -- CTT_EMISSAO    |     |    |    | DAT NN             |
   -- CTT_VALOR      |     |    |    | NUM(15,2) NN       |
   -- CTT_FATURADO   |     |    |    | NUM(15,2) NN       |
   -- CTT_NUMERO     |     |    |    | VC(10) NN          |
   -- CTT_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- CTT_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- CTT_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D15         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--     
-------------------------------------------------------------------------------------
--                                C S T I C M S                                    --
--tblcsticms
-------------------------------------------------------------------------------------
CREATE TABLE CSTICMS(
  ICMS_CODIGO VARCHAR(3) NOT NULL
  ,ICMS_ENTSAI VARCHAR(1) NOT NULL
  ,ICMS_NOME VARCHAR(60) NOT NULL
  ,ICMS_SNALIQ VARCHAR(1) NOT NULL
  ,ICMS_PCISENTAS NUMERIC(6,2) NOT NULL
  ,ICMS_PCOUTRAS NUMERIC(6,2) NOT NULL
  ,ICMS_REDUCAOBC VARCHAR(1) NOT NULL
  ,ICMS_ATIVO VARCHAR(1) NOT NULL
  ,ICMS_REG VARCHAR(1) NOT NULL
  ,ICMS_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_IcmsEntSai CHECK( ICMS_ENTSAI IN('E','S'))  
  ,CONSTRAINT chk_IcmsSnAliq CHECK( ICMS_SNALIQ IN('S','N'))    
  ,CONSTRAINT chk_IcmsReducaoBc CHECK( ICMS_REDUCAOBC IN('S','N'))  
  ,CONSTRAINT chk_IcmsAtivo CHECK( ICMS_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_IcmsReg CHECK( ICMS_REG IN('A','P','S'))
  ,CONSTRAINT PKCSTICMS PRIMARY KEY (ICMS_CODIGO,ICMS_ENTSAI));
GO
CREATE VIEW VCSTICMS AS
  SELECT ICMS_CODIGO,ICMS_NOME,ICMS_ENTSAI,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR FROM CSTICMS
GO
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('00','S','TRIBUTADA INTEGRALMENTE'        ,'S',0.00   ,0.00   ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('10','S','TRIB COBRA ICMS POR ST'         ,'S',0.00   ,0.00   ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('20','S','COM REDUCAO DE BASE DE CALCULO' ,'S',100.00 ,0.00   ,'S','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('40','S','ISENTA'                         ,'N',100.00 ,0.00   ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('41','S','NAO TRIBUTADA'                  ,'N',100.00 ,0.00   ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('50','S','COM SUSPENSAO'                  ,'N',0.00   ,100.00 ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('51','S','COM DIFERIMENTO'                ,'N',0.00   ,100.00 ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('60','S','ICMS COBRADO ANT POR ST'        ,'N',0.00   ,100.00 ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('70','S','COM RED BC COBRANCA ICMS POR ST','S',100.00 ,0.00   ,'S','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('90','S','OUTRAS'                         ,'S',0.00   ,100.00 ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('00','E','TRIBUTADA INTEGRALMENTE'        ,'S',0.00   ,0.00   ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('20','E','COM REDUCAO DE BASE DE CALCULO' ,'S',100.00 ,0.00   ,'S','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('40','E','ISENTA'                         ,'N',100.00 ,0.00   ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('41','E','NAO TRIBUTADA'                  ,'N',100.00 ,0.00   ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('50','E','COM SUSPENSAO'                  ,'N',0.00   ,100.00 ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('51','E','COM DIFERIMENTO'                ,'N',0.00   ,100.00 ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('60','E','ICMS COBRADO ANT POR ST'        ,'N',0.00   ,100.00 ,'N','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('70','E','COM RED BC COBRANCA ICMS POR ST','S',100.00 ,0.00   ,'S','S','P',1);
INSERT INTO VCSTICMS(ICMS_CODIGO,ICMS_ENTSAI,ICMS_NOME,ICMS_SNALIQ,ICMS_PCISENTAS,ICMS_PCOUTRAS,ICMS_REDUCAOBC,ICMS_ATIVO,ICMS_REG,ICMS_CODUSR) VALUES('90','E','OUTRAS'                         ,'S',0.00   ,100.00 ,'N','S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- ICMS_CODIGO    | PK  |    |    | VC(03) NN          |  
   -- ICMS_ENTSAI    |PK/CC|    |    | VC(1) NN           |  
   -- ICMS_NOME      |     |    |    | VC(60) NN          |
   -- ICMS_SNALIQ    | CC  |    |    | VC(1) NN           |  
   -- ICMS_PCISENTAS |     |    |    | NUM(6,2) NN        | Soma de Isentas+Outras deve ser 0 ou 100
   -- ICMS_PCOUTRAS  |     |    |    | NUM(6,2) NN        | Soma de Isentas+Outras deve ser 0 ou 100
   -- ICMS_REDUCAOBC | CC  |    |    | VC(1) NN           |    
   -- ICMS_ATIVO     | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- ICMS_REG       | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- ICMS_CODUSR    | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D14         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                C S T I P I                                      --
--tblcstipi
-------------------------------------------------------------------------------------
GO
CREATE TABLE CSTIPI(
  IPI_CODIGO VARCHAR(3) NOT NULL
  ,IPI_ENTSAI VARCHAR(1) NOT NULL  
  ,IPI_NOME VARCHAR(60) NOT NULL  
  ,IPI_SNALIQ VARCHAR(1) NOT NULL
  ,IPI_PCISENTAS NUMERIC(6,2) NOT NULL
  ,IPI_PCOUTRAS NUMERIC(6,2) NOT NULL
  ,IPI_ATIVO VARCHAR(1) NOT NULL
  ,IPI_REG VARCHAR(1) NOT NULL
  ,IPI_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_IpiEntSai CHECK( IPI_ENTSAI IN('E','S'))  
  ,CONSTRAINT chk_IpiSnAliq CHECK( IPI_SNALIQ IN('S','N'))    
  ,CONSTRAINT chk_IpiAtivo CHECK( IPI_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_IpiReg CHECK( IPI_REG IN('A','P','S'))
  ,CONSTRAINT PKCSTIPI PRIMARY KEY (IPI_CODIGO,IPI_ENTSAI));
GO
CREATE VIEW VCSTIPI AS
  SELECT IPI_CODIGO,IPI_NOME,IPI_ENTSAI,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR FROM CSTIPI
GO
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('00','E','ENTRADA RECUP CREDITO'       ,'S',0.00   ,0.00   ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('01','E','ENTRADA TRIB ALIQ ZERO'      ,'N',100.00 ,0.00   ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('02','E','ENTRADA ISENTA'              ,'N',100.00 ,0.00   ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('03','E','ENTRADA NAO TRIBUTADA MESMO' ,'N',0.00   ,100.00 ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('04','E','ENTRADA IMUNE'               ,'N',0.00   ,100.00 ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('05','E','ENTRADA COM SUSPENSAO'       ,'N',0.00   ,100.00 ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('49','E','OUTRAS ENTRADAS'             ,'S',0.00   ,0.00   ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('50','S','SAIDA TRIBUTADA'             ,'S',0.00   ,0.00   ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('51','S','SAIDA TRIB ALIQ ZERO'        ,'N',100.00 ,0.00   ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('52','S','SAIDA ISENTA'                ,'N',100.00 ,0.00   ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('53','S','SAIDA NAO TRIBUTADA'         ,'N',100.00 ,0.00   ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('54','S','SAIDA IMUNE'                 ,'N',0.00   ,100.00 ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('55','S','SAIDA COM SUSPENSAO'         ,'N',0.00   ,100.00 ,'S','P',1);
INSERT INTO VCSTIPI(IPI_CODIGO,IPI_ENTSAI,IPI_NOME,IPI_SNALIQ,IPI_PCISENTAS,IPI_PCOUTRAS,IPI_ATIVO,IPI_REG,IPI_CODUSR) VALUES('99','S','OUTRAS SAIDAS'               ,'S',0.00   ,0.00   ,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- IPI_CODIGO     | PK  |    |    | VC(03) NN          |
   -- IPI_ENTSAI     |PK/CC|    |    | VC(1) NN           |
   -- IPI_NOME       |     |    |    | VC(60) NN          |
   -- IPI_SNALIQ     | CC  |    |    | VC(1) NN           |    
   -- IPI_PCISENTAS  |     |    |    | NUM(6,2) NN        | Soma de Isentas+Outras deve ser 0 ou 100
   -- IPI_PCOUTRAS   |     |    |    | NUM(6,2) NN        | Soma de Isentas+Outras deve ser 0 ou 100
   -- IPI_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- IPI_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- IPI_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D14         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                C S T P I S                                      --
--tblcstpis
-------------------------------------------------------------------------------------
GO
 CREATE TABLE CSTPIS(
  PIS_CODIGO VARCHAR(3) NOT NULL
  ,PIS_ENTSAI VARCHAR(1) NOT NULL
  ,PIS_NOME VARCHAR(60) NOT NULL
  ,PIS_SNALIQ VARCHAR(1) NOT NULL
  ,PIS_PCISENTAS NUMERIC(6,2) NOT NULL
  ,PIS_PCOUTRAS NUMERIC(6,2) NOT NULL
  ,PIS_ATIVO VARCHAR(1) NOT NULL
  ,PIS_REG VARCHAR(1) NOT NULL
  ,PIS_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_PisEntSai CHECK( PIS_ENTSAI IN('E','S'))  
  ,CONSTRAINT chk_PisSnAliq CHECK( PIS_SNALIQ IN('S','N'))      
  ,CONSTRAINT chk_PisAtivo CHECK( PIS_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PisReg CHECK( PIS_REG IN('A','P','S'))
  ,CONSTRAINT PKCSTPIS PRIMARY KEY (PIS_CODIGO,PIS_ENTSAI));
GO
CREATE VIEW VCSTPIS AS
  SELECT PIS_CODIGO,PIS_NOME,PIS_ENTSAI,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR FROM CSTPIS
GO
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('01','S','OPERACAO TRIBUTAV ALIQ BASICA'                               ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('02','S','OPERACAO TRIBUTAV ALIQ DIFERENCIADA'                         ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('03','S','OPERACAO TRIBUTAV ALIQ POR UNID MEDIDA PRODUTO'              ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('04','S','OPERACAO TRIBUTAV MOFASICA-REVENDA ALIQ ZERO'                ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('05','S','OPERACAO TRIBUTAV SUBSTIT TRIBUTARIA'                        ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('06','S','OPERACAO TRIBUTAV ALIQ ZERO'                                 ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('07','S','OPERACAO ISENTA CONTRIBUICAO'                                ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('08','S','OPER SEM INCIDENCIA CONTRIBUICAO'                            ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('09','S','OPER COM SUSPENSAO da CONTRIBUICAO'                          ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('49','S','OUTRAS OPERAC SAIDA'                                         ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('50','S','OPER DIREITO CRED VINC RECEITA TRIB MERC INTERNO'            ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('51','S','OPER DIREITO CRED VINC RECEITA NAO TRIB MERC INTERNO'        ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('52','S','OPER DIREITO CRED VINC RECEITA EXPORT'                       ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('53','S','OPER DIREITO CRED VINC RECEITA TRIB NAO TRIB MERC INT'       ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('54','S','OPER DIREITO CRED VINC RECEITA TRIB MERC INTERNO/EXP'        ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('55','S','OPER DIREITO CRED VINC RECEITA NAO TRIB MERC INT/EXP'        ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('56','S','OPER DIREITO CRED VINC RECEITA TRIB N TRIB MERC INT/EXP'     ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('60','S','CRED PRESUM OPER AQUIS VINCUL RECEITA TRIB MERC INTERNO'     ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('61','S','CRED PRESUM OPER AQUIS VINCUL RECEITA NAO TRIB MERC INT'     ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('62','S','CRED PRESUM OPER AQUIS VINCUL RECEITA EXPORT'                ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('63','S','CRED PRESUM OPER AQUIS VINCUL RECEITA TRIB N TRIB MERC INT'  ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('64','S','CRED PRESUM OPER AQUIS VINCUL RECEITA TRIB MERC INT/EXP'     ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('65','S','CRED PRESUM OPER AQUIS VINCUL RECEITA NAO TRIB MERC INT/EXP' ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('66','S','CRED PRESUM OPER AQUIS VINCUL RECEITA TRIB N TRIB MERC I/E'  ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('67','S','CRED PRESUM OUTRAS OPERAC'                                   ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('70','S','OPER AQUISICAO SEM DIREITO CRED'                             ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('71','S','OPER AQUISICAO COM ISENCAO'                                  ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('72','S','OPER AQUISICAO COM SUSPENSAO'                                ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('73','S','OPER AQUISICAO ALIQ ZERO'                                    ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('74','S','OPER AQUISICAO SEM INCIDENCIA CONTRIBUICAO'                  ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('75','S','OPER AQUISICAO SUBSTIT TRIBUTARIA'                           ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('98','S','OUTRAS OPERAC ENTRADA'                                       ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTPIS(PIS_CODIGO,PIS_ENTSAI,PIS_NOME,PIS_SNALIQ,PIS_PCISENTAS,PIS_PCOUTRAS,PIS_ATIVO,PIS_REG,PIS_CODUSR) VALUES('99','S','OUTRAS OPERAC'                                               ,'S',100.00,0.00,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- PIS_CODIGO     | PK  |    |    | VC(03) NN          |
   -- PIS_ENTSAI     |PK/CC|    |    | VC(1) NN           |
   -- PIS_NOME       |     |    |    | VC(60) NN          |
   -- PIS_SNALIQ     | CC  |    |    | VC(1) NN           |      
   -- PIS_PCISENTAS  |     |    |    | NUM(6,2) NN        | Soma de Isentas+Outras deve ser 0 ou 100
   -- PIS_PCOUTRAS   |     |    |    | NUM(6,2) NN        | Soma de Isentas+Outras deve ser 0 ou 100
   -- PIS_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PIS_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PIS_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D14         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                C S T S I M P L E S                              --
--tblcstsimples
-------------------------------------------------------------------------------------
GO
CREATE TABLE CSTSIMPLES(
  SN_CODIGO VARCHAR(3) NOT NULL
  ,SN_ENTSAI VARCHAR(1) NOT NULL
  ,SN_NOME VARCHAR(60) NOT NULL
  ,SN_SNALIQ VARCHAR(1) NOT NULL
  ,SN_PCISENTAS NUMERIC(6,2) NOT NULL
  ,SN_PCOUTRAS NUMERIC(6,2) NOT NULL
  ,SN_REDUCAOBC VARCHAR(1) NOT NULL
  ,SN_ATIVO VARCHAR(1) NOT NULL
  ,SN_REG VARCHAR(1) NOT NULL
  ,SN_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_SnEntSai CHECK( SN_ENTSAI IN('E','S'))  
  ,CONSTRAINT chk_SnSnAliq CHECK( SN_SNALIQ IN('S','N'))    
  ,CONSTRAINT chk_SnReducaoBc CHECK( SN_REDUCAOBC IN('S','N'))  
  ,CONSTRAINT chk_SnAtivo CHECK( SN_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_SnReg CHECK( SN_REG IN('A','P','S'))
  ,CONSTRAINT PKCSTSIMPLES PRIMARY KEY (SN_CODIGO,SN_ENTSAI));
GO
CREATE VIEW VCSTSIMPLES AS
  SELECT SN_CODIGO,SN_NOME,SN_ENTSAI,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR FROM CSTSIMPLES
GO
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('101', 'S','PERMITE APROVEITAMENTO DE CREDITO DO ICMS'        ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('102', 'S','NAO PERMITE APROVEITAMENTO DE CREDITO DO ICMS'    ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('103', 'S','OPERACAO ISENTA DE ICMS'                          ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('201', 'S','PERMITE APROVEITAMENTO DE CREDITO DO ICMS-ST'     ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('202', 'S','NAO PERMITE APROVEITAMENTO DE CREDITO DO ICMS-ST' ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('203', 'S','OPERACAO ISENTA DE ICMS E ICMS-ST'                ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('300', 'S','OPERACAO IMUNE DE ICMS'                           ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('400', 'S','OPERACAO TOTALMENTE ISENTA DE ICMS'               ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('500', 'S','OPERACAO COM ICMS-ST ANTECIPADO'                  ,'S',100.00,0.00,'S','P',1);
INSERT INTO VCSTSIMPLES(SN_CODIGO,SN_ENTSAI,SN_NOME,SN_SNALIQ,SN_PCISENTAS,SN_PCOUTRAS,SN_ATIVO,SN_REG,SN_CODUSR) VALUES('900', 'S','OUTRAS OPERACOES'                                 ,'S',100.00,0.00,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- SN_CODIGO      | PK  |    |    | VC(03) NN          |
   -- SN_ENTSAI      |PK/CC|    |    | VC(1) NN           |
   -- SN_NOME        |     |    |    | VC(60) NN          |
   -- SN_SNALIQ      | CC  |    |    | VC(1) NN           |       
   -- SN_PCISENTAS   |     |    |    | NUM(6,2) NN        | Soma de Isentas+Outras deve ser 0 ou 100
   -- SN_PCOUTRAS    |     |    |    | NUM(6,2) NN        | Soma de Isentas+Outras deve ser 0 ou 100
   -- SN_REDUCAOBC   | CC  |    |    | VC(1) NN           |      
   -- SN_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- SN_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- SN_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D14         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                     E M A I L                                   --
--tblemail
-------------------------------------------------------------------------------------
GO
CREATE TABLE EMAIL(
  EMA_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,EMA_ROTINA VARCHAR(20) NOT NULL
  ,EMA_EMAIL VARCHAR(50) NOT NULL
  ,EMA_ATIVO VARCHAR(1) NOT NULL
  ,EMA_REG VARCHAR(1) NOT NULL
  ,EMA_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_EmaAtivo CHECK( EMA_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_EmaReg CHECK( EMA_REG IN('A','P','S')));
GO  
CREATE VIEW VEMAIL AS
  SELECT EMA_CODIGO,EMA_ROTINA,EMA_EMAIL,EMA_ATIVO,EMA_REG,EMA_CODUSR FROM EMAIL
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- EMA_CODIGO     | PK  |    |    | INT                |  Auto incremento
   -- EMA_ROTINA     |     |    |    | VC(20) NN          |  
   -- EMA_EMAIL      |     |    |    | VC(20) NN          |    
   -- EMA_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- EMA_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- EMA_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D16         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--  
-------------------------------------------------------------------------------------
--                               E M B A L A G E M                                 --
--tblembalagem
-------------------------------------------------------------------------------------
GO
CREATE TABLE EMBALAGEM(
  EMB_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,EMB_NOME VARCHAR(30) NOT NULL
  ,EMB_ATIVO VARCHAR(1) NOT NULL
  ,EMB_REG VARCHAR(1) NOT NULL
  ,EMB_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_EmbAtivo CHECK( EMB_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_EmbReg CHECK( EMB_REG IN('A','P','S')));
GO
CREATE VIEW VEMBALAGEM AS
  SELECT EMB_CODIGO,EMB_NOME,EMB_ATIVO,EMB_REG,EMB_CODUSR FROM EMBALAGEM
GO
INSERT INTO VEMBALAGEM VALUES('BD' ,'BALDE'           ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('BR' ,'BR'              ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('CJ' ,'CONJUNTO'        ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('CO' ,'CONCERTO'        ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('CT' ,'CENTO'           ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('CX' ,'CAIXA'           ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('FD' ,'FARDO'           ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('GL' ,'GALAO'           ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('HR' ,'HORA'            ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('LT' ,'LITRO'           ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('M'  ,'METRO'           ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('M2' ,'METRO QUADRADO'  ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('M3' ,'METRO CUBICO'    ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('PC' ,'PECA'            ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('PCT','PACOTE'          ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('RL' ,'ROLO'            ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('UN' ,'UNIDADE'         ,'S'  ,'S'  ,1);
INSERT INTO VEMBALAGEM VALUES('VD' ,'VIDRO'           ,'S'  ,'S'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- EMB_CODIGO     | PK  |    |    | VC(03) NN          |
   -- EMB_NOME       |     |    |    | VC(30) NN          |
   -- EMB_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- EMB_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- EMB_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D24         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                 E M P R E S A                                   --
--tblempresa
-------------------------------------------------------------------------------------
CREATE TABLE EMPRESA(
  EMP_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,EMP_NOME VARCHAR(40) NOT NULL
  ,EMP_APELIDO VARCHAR(15) NOT NULL
  ,EMP_CNPJ VARCHAR(14) NOT NULL
  ,EMP_INS VARCHAR(19) DEFAULT 'NSA' NOT NULL
  ,EMP_CODCDD VARCHAR(7) NOT NULL           --CIDADE
  ,EMP_CODLGR VARCHAR(5) NOT NULL           --LOGRADOURO
  ,EMP_ENDERECO VARCHAR(60) NOT NULL
  ,EMP_NUMERO VARCHAR(10) NOT NULL
  ,EMP_CEP VARCHAR(8) NOT NULL              
  ,EMP_BAIRRO VARCHAR(15) NOT NULL
  ,EMP_FONE VARCHAR(10)
  ,EMP_CODETF VARCHAR(3) NOT NULL                   --EMPRESATRIBFED
  ,EMP_ALIQCOFINS NUMERIC(6,2) NOT NULL
  ,EMP_ALIQPIS NUMERIC(6,2) NOT NULL
  ,EMP_ALIQCSLL NUMERIC(6,2) NOT NULL
  ,EMP_ALIQIRRF NUMERIC(6,2) NOT NULL  
  ,EMP_BCPRESUMIDO NUMERIC(6,2) NOT NULL
  ,EMP_ALIQIRPRESUMIDO NUMERIC(6,2) NOT NULL
  ,EMP_ALIQCSLLPRESUMIDO NUMERIC(6,2) NOT NULL
  ,EMP_ANEXOSIMPLES INTEGER NOT NULL
  ,EMP_CODETP VARCHAR(3) NOT NULL                   --EMPRESATIPO
  ,EMP_CODERM VARCHAR(3) NOT NULL                   --EMPRESARAMO
  ,EMP_SMTPUSERNAME VARCHAR(60)
  ,EMP_SMTPPASSWORD VARCHAR(30)
  ,EMP_SMTPHOST VARCHAR(30)
  ,EMP_SMTPPORT VARCHAR(4)
  ,EMP_CERTPATH VARCHAR(100)
  ,EMP_CERTSENHA VARCHAR(20)
  ,EMP_CERTVALIDADE DATE
  ,EMP_PRODHOMOL VARCHAR(1) NOT NULL
  ,EMP_CONTINGENCIA VARCHAR(1) NOT NULL
  ,EMP_CODERT VARCHAR(3) NOT NULL                   --EMPRESAREGTRIB
  ,EMP_ATIVO VARCHAR(1) NOT NULL
  ,EMP_REG VARCHAR(1) NOT NULL
  ,EMP_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_EmpCnpj CHECK( EMP_CNPJ NOT LIKE '%[^0-9]%' )        
  ,CONSTRAINT chk_EmpCep CHECK( EMP_CEP LIKE('[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]'))    
  ,CONSTRAINT chk_EmpProdHomol CHECK( EMP_PRODHOMOL IN('P','H'))  
  ,CONSTRAINT chk_EmpContingencia CHECK( EMP_CONTINGENCIA IN('S','N'))  
  ,CONSTRAINT chk_EmpAtivo CHECK( EMP_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_EmpReg CHECK( EMP_REG IN('A','P','S')));
GO
CREATE VIEW VEMPRESA AS
  SELECT EMP_CODIGO
         ,EMP_NOME
         ,EMP_APELIDO
         ,EMP_CNPJ
         ,EMP_INS
         ,EMP_CODCDD
         ,EMP_CODLGR
         ,EMP_ENDERECO
         ,EMP_NUMERO
         ,EMP_CEP
         ,EMP_BAIRRO
         ,EMP_FONE
         ,EMP_CODETF
         ,EMP_ALIQCOFINS
         ,EMP_ALIQPIS
         ,EMP_ALIQCSLL
         ,EMP_BCPRESUMIDO
         ,EMP_ALIQIRPRESUMIDO
         ,EMP_ALIQCSLLPRESUMIDO
         ,EMP_ALIQIRRF
         ,EMP_ANEXOSIMPLES
         ,EMP_CODETP
         ,EMP_CODERM
         ,EMP_SMTPUSERNAME
         ,EMP_SMTPPASSWORD
         ,EMP_SMTPHOST
         ,EMP_SMTPPORT
         ,EMP_CERTPATH
         ,EMP_CERTSENHA
         ,EMP_CERTVALIDADE
         ,EMP_PRODHOMOL
         ,EMP_CONTINGENCIA
         ,EMP_CODERT
         ,EMP_ATIVO
         ,EMP_REG
         ,EMP_CODUSR
    FROM EMPRESA 
   -- ----------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO                 |INS  |UPD |DEL | TIPO               | Obs
   -- ----------------------|-----|----|----|--------------------|----------------------------------------------------------
   -- EMP_CODIGO            | PK  |    |    | INT                |  Auto incremento
   -- EMP_NOME              |     |    |    | VC(40) NN          |
   -- EMP_APELIDO           |     |    |    | VC(15) NN          |  
   -- EMP_CNPJ              |     |    |    | VC(14) NN          |   
   -- EMP_INS               |     |    |    | VC(9) NN           |   
   -- EMP_CODCDD            | SEL |    |    | VC(7) NN           | Campo relacionado (CIDADE)   
   -- CDD_NOME              | SEL |    |    | VC(30) NN          | Campo relacionado (CIDADE)      
   -- EMP_CODLGR            | SEL |    |    | VC(5) NN           | Campo relacionado (LOGRADOURO)   
   -- LGR_NOME              | SEL |    |    | VC(20) NN          | Campo relacionado (LOGRADOURO)      
   -- EMP_ENDERECO          |     |    |    | VC(60) NN          |
   -- EMP_NUMERO            |     |    |    | VC(10) NN          |   
   -- EMP_CEP               |     |    |    | VC(8) NN           |
   -- EMP_BAIRRO            |     |    |    | VC(15) NN          |   
   -- EMP_FONE              |     |    |    | VC(10) NN          |      
   -- EMP_CODETF            | SEL |    |    | VC(3) NN           | Campo relacionado (EMPRESATRIBFED)
   -- ETF_NOME              | SEL |    |    | VC(20) NN          | Campo relacionado (EMPRESATRIBFED)
   -- EMP_ALIQCOFINS        |     |    |    | NUM(6,2) NN        |      
   -- EMP_ALIQPIS           |     |    |    | NUM(6,2) NN        |      
   -- EMP_ALIQCSLL          |     |    |    | NUM(6,2) NN        |      
   -- EMP_BCPRESUMIDO       |     |    |    | NUM(6,2) NN        |      
   -- EMP_ALIQIRPRESUMIDO   |     |    |    | NUM(6,2) NN        |      
   -- EMP_ALIQCSLLPRESUMIDO |     |    |    | NUM(6,2) NN        |      
   -- EMP_ALIQIRRF          |     |    |    | NUM(6,2) NN        |      
   -- EMP_ANEXOSIMPLES      |     |    |    | INT                |
   -- EMP_CODETP            | SEL |    |    | VC(3) NN           | Campo relacionado (EMPRESATIPO)
   -- ETP_NOME              | SEL |    |    | VC(20) NN          | Campo relacionado (EMPRESATIPO)
   -- EMP_CODERM            | SEL |    |    | VC(3) NN           | Campo relacionado (EMPRESARAMO)
   -- ERM_NOME              | SEL |    |    | VC(25) NN          | Campo relacionado (EMPRESARAMO)
   -- EMP_CODERT            | SEL |    |    | VC(3) NN           | Campo relacionado (EMPRESAREGTRIB)
   -- ERT_NOME              | SEL |    |    | VC(25) NN          | Campo relacionado (EMPRESAREGTRIB)
   -- EMP_SMTPUSERNAME      |     |    |    | VC(60)             |
   -- EMP_SMTPPASSWORD      |     |    |    | VC(30)             |
   -- EMP_SMTPHOST          |     |    |    | VC(30)             |
   -- EMP_SMTPPORT          |     |    |    | VC(4)              |
   -- EMP_CERTPATH          |     |    |    | VC(100)            |
   -- EMP_CERTSENHA         |     |    |    | VC(20)             |
   -- EMP_CERTVALIDADE      |     |    |    | DAT                |
   -- EMP_PRODHOMOL         | CC  |    |    | VC(1) NN           |  
   -- EMP_CONTINGENCIA      | CC  |    |    | VC(1) NN           |    
   -- EMP_ATIVO             | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- EMP_REG               | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- EMP_CODUSR            | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO           | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB            | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D03                | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31                | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ----------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                             E M P R E S A R A M O                               --
--tblempresaramo
-------------------------------------------------------------------------------------
CREATE TABLE EMPRESARAMO(
  ERM_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,ERM_NOME VARCHAR(25) NOT NULL
  ,ERM_ATIVO VARCHAR(1) NOT NULL
  ,ERM_REG VARCHAR(1) NOT NULL
  ,ERM_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_ErmAtivo CHECK( ERM_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_ErmReg CHECK( ERM_REG IN('A','P','S'))
);
GO
CREATE VIEW VEMPRESARAMO AS
  SELECT ERM_CODIGO,ERM_NOME,ERM_ATIVO,ERM_REG,ERM_CODUSR FROM EMPRESARAMO
GO
INSERT INTO VEMPRESARAMO VALUES('ALI' ,'ALIMENTICIO'            ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('BRI' ,'BRINQUEDOS'             ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('CER' ,'CEREALISTA'             ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('COS' ,'COSMETICO'              ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('EE'  ,'ELETRO ELETRONICO'      ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('HPL' ,'HIGIENE PESS/LIMPREZA'  ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('MAD' ,'MADEIREIRA'             ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('MC'  ,'MATERIAL CONSTRUCAO'    ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('ME'  ,'MATERIAL ELETRICO'      ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('MED' ,'MEDICAMENTOS'           ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('PAP' ,'PAPELARIA'              ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('SER' ,'SERVICO'                ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('TV'  ,'TINTA E VERNIZ'         ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('VEI' ,'VEICULOS'               ,'S','P',1);
INSERT INTO VEMPRESARAMO VALUES('VES' ,'VESTUARIO'              ,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- ERM_CODIGO     | PK  |    |    | VC(03) NN          |
   -- ERM_NOME       |     |    |    | VC(25) NN          |
   -- ERM_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- ERM_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- ERM_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D25         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             E M P R E S A R E G T R I B                         --
--tblempresaregtrib
-------------------------------------------------------------------------------------
CREATE TABLE EMPRESAREGTRIB(
  ERT_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,ERT_NOME VARCHAR(50) NOT NULL
  ,ERT_ATIVO VARCHAR(1) NOT NULL
  ,ERT_REG VARCHAR(1) NOT NULL
  ,ERT_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_ErtAtivo CHECK( ERT_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_ErtReg CHECK( ERT_REG IN('A','P','S'))
);
GO
CREATE VIEW VEMPRESAREGTRIB AS
  SELECT ERT_CODIGO,ERT_NOME,ERT_ATIVO,ERT_REG,ERT_CODUSR FROM EMPRESAREGTRIB
GO
INSERT INTO VEMPRESAREGTRIB VALUES('1','SIMPLES NAC'                              ,'S','P',1);
INSERT INTO VEMPRESAREGTRIB VALUES('2','SIMPLES NAC EXCESSO SUBLIME RECEITA BRUTA','S','P',1);
INSERT INTO VEMPRESAREGTRIB VALUES('3','REGIME NORMAL'                            ,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- ERT_CODIGO     | PK  |    |    | VC(03) NN          |
   -- ERT_NOME       |     |    |    | VC(30) NN          |
   -- ERT_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- ERT_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- ERT_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D25         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             E M P R E S A T I P O                               --
--tblempresatipo
-------------------------------------------------------------------------------------
CREATE TABLE EMPRESATIPO(
  ETP_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,ETP_NOME VARCHAR(20) NOT NULL
  ,ETP_ATIVO VARCHAR(1) NOT NULL
  ,ETP_REG VARCHAR(1) NOT NULL
  ,ETP_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_EtpAtivo CHECK( ETP_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_EtpReg CHECK( ETP_REG IN('A','P','S'))
);
GO
CREATE VIEW VEMPRESATIPO AS
  SELECT ETP_CODIGO,ETP_NOME,ETP_ATIVO,ETP_REG,ETP_CODUSR FROM EMPRESATIPO
GO
INSERT INTO VEMPRESATIPO VALUES('ATA','ATACADISTA','S','P',1);
INSERT INTO VEMPRESATIPO VALUES('IMP','IMPORTADORA','S','P',1);
INSERT INTO VEMPRESATIPO VALUES('IND','INDUSTRIA','S','P',1);
INSERT INTO VEMPRESATIPO VALUES('SER','SERVICO','S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- ETP_CODIGO     | PK  |    |    | VC(03) NN          |
   -- ETP_NOME       |     |    |    | VC(20) NN          |
   -- ETP_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- ETP_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- ETP_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D25         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             E M P R E S A T R I B F E D                         --
--tblempresatribfed
-------------------------------------------------------------------------------------
CREATE TABLE EMPRESATRIBFED(
  ETF_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,ETF_NOME VARCHAR(20) NOT NULL
  ,ETF_ATIVO VARCHAR(1) NOT NULL
  ,ETF_REG VARCHAR(1) NOT NULL
  ,ETF_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_EtfAtivo CHECK( ETF_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_EtfReg CHECK( ETF_REG IN('A','P','S')));
GO
CREATE VIEW VEMPRESATRIBFED AS
  SELECT ETF_CODIGO,ETF_NOME,ETF_ATIVO,ETF_REG,ETF_CODUSR FROM EMPRESATRIBFED
GO
INSERT INTO VEMPRESATRIBFED VALUES('S','SIMPLES','S','P',1);
INSERT INTO VEMPRESATRIBFED VALUES('P','PRESUMIDO','S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- ETF_CODIGO     | PK  |    |    | VC(03) NN          |
   -- ETF_NOME       |     |    |    | VC(20) NN          |
   -- ETF_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- ETF_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- ETF_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D25         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
/*
CREATE TABLE FECHAMESSIMPLES(
  FMS_CODMES INTEGER NOT NULL,
  FMS_CODEMP INTEGER NOT NULL,
  FMS_VLRMES NUMERIC(15,2) NOT NULL,
  FMS_VLRACUMULADO NUMERIC(15,2) NOT NULL,
  FMS_ALIQUOTA NUMERIC(15,4) NOT NULL,
  SYS_MESINI INTEGER NOT NULL,
  SYS_MESFIN INTEGER NOT NULL,
  SYS_ATUALIZAR INTEGER NOT NULL,
  SYS_ANEXO INTEGER NOT NULL,
  SYS_ITEM INTEGER NOT NULL,
  FMS_SYS VARCHAR(1) NOT NULL,
  FMS_CODDIR INTEGER NOT NULL,
  FMS_CODUSR INTEGER NOT NULL,
CONSTRAINT PKTFECHAMESSIMPLES PRIMARY KEY (FMS_CODMES, FMS_CODEMP)
);
*/
-------------------------------------------------------------------------------------
--                                E S T A D O                                      --
--tblestado
-------------------------------------------------------------------------------------
GO
CREATE TABLE dbo.ESTADO(
  EST_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,EST_NOME VARCHAR(20) NOT NULL
  ,EST_ALIQICMS NUMERIC(15,02) NOT NULL
  ,EST_CODREG VARCHAR(5) NOT NULL
  ,EST_ATIVO VARCHAR(1) NOT NULL
  ,EST_REG VARCHAR(1) NOT NULL
  ,EST_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_EstAtivo CHECK( EST_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_EstReg CHECK( EST_REG IN('A','P','S'))
);
GO
CREATE VIEW VESTADO AS
  SELECT EST_CODIGO,EST_NOME,EST_ALIQICMS,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR FROM ESTADO
GO
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('SP','SAO PAULO'            ,18,'SD'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('RJ','RIO DE JANEIRO'       ,18,'SD'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('MG','MINAS GERAIS'         ,18,'SD'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('AC','ACRE'                 ,18,'NO'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('AL','ALAGOAS'              ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('AM','AMAZONAS'             ,18,'NO'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('AP','AMAPA'                ,18,'NO'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('BA','BAHIA'                ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('CE','CEARA'                ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('DF','DISTRITO FEDERAL'     ,18,'CO'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('ES','ESPIRITO SANTO'       ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('GO','GOIAS'                ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('MA','MARANHAO'             ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('MT','MATO GROSSO'          ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('PA','PARA'                 ,18,'NO'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('PB','PARAIBA'              ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('PE','PERNAMBUCO'           ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('PI','PIAUI'                ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('PR','PARANA'               ,18,'SUL' ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('RN','RIO GRANDE DO NORTE'  ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('RO','RONDONIA'             ,18,'NO'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('RR','RORAIMA'              ,18,'NO'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('RS','RIO GRANDE DO SUL'    ,18,'SUL' ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('SC','SANTA CATARINA'       ,18,'SUL' ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('SE','SERGIPE'              ,18,'ND'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('TO','TOCANTINS'            ,18,'NO'  ,'S','P',1);
INSERT INTO VESTADO(EST_CODIGO,EST_NOME,EST_CODREG,EST_ATIVO,EST_REG,EST_CODUSR) VALUES('MS','MATO GROSSO SUL'      ,18,'ND'  ,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- EST_CODIGO     | PK  |    |    | VC(03) NN          |
   -- EST_NOME       |     |    |    | VC(20) NN          |
   -- EST_ALIQICMS   |     |    |    | NUM(6,2) NN        |        
   -- EST_CODERG     | SEL |    |    | VC(5) NN           | Campo relacionado (REGIAO)
   -- REG_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (REGIAO)
   -- EST_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- EST_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- EST_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D08         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                               F A V O R E C I D O                               --
-- tblfavorecido                                                                   --
-------------------------------------------------------------------------------------
GO
CREATE TABLE FAVORECIDO(
  FVR_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,FVR_NOME VARCHAR(60) NOT NULL  
  ,FVR_APELIDO VARCHAR(15) NOT NULL
  ,FVR_BAIRRO VARCHAR(15) NOT NULL
  ,FVR_CNPJCPF VARCHAR(14) NOT NULL
  ,FVR_CEP VARCHAR(8) NOT NULL  
  ,FVR_CODCDD VARCHAR(7) NOT NULL           --CIDADE
  ,FVR_DTCADASTRO DATE DEFAULT GETDATE() NOT NULL
  ,FVR_FISJUR VARCHAR(1) NOT NULL
  ,FVR_INSMUNIC VARCHAR(20)
  ,FVR_CONTATO VARCHAR(40)
  ,FVR_ENDERECO VARCHAR(60)
  ,FVR_FONE VARCHAR(10)
  ,FVR_INS VARCHAR(19)
  ,FVR_CTAATIVO VARCHAR(15)               --CONTAGERENCIAL
  ,FVR_CTAPASSIVO VARCHAR(15)             --CONTAGERENCIAL
  ,FVR_CADMUNIC VARCHAR(20)
  ,FVR_EMAIL VARCHAR(60)
  ,FVR_CODCTG VARCHAR(3) NOT NULL        -- CATEGORIA
  ,FVR_SENHA VARCHAR(10)
  ,FVR_COMPLEMENTO VARCHAR(60)
  ,FVR_NUMERO VARCHAR(10) NOT NULL
  ,FVR_CODLGR VARCHAR(5) NOT NULL        --LOGRADOURO
  ,FVR_GCCP INTEGER NOT NULL
  ,FVR_GCCR INTEGER NOT NULL
  ,FVR_ATIVO VARCHAR(1) NOT NULL
  ,FVR_REG VARCHAR(1) NOT NULL
  ,FVR_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_FvrCnpjCpf CHECK( FVR_CNPJCPF NOT LIKE '%[^0-9]%' )    
  ,CONSTRAINT chk_FvrCep CHECK( FVR_CEP LIKE('[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]'))  
  ,CONSTRAINT chk_FvrFisJur CHECK( FVR_FISJUR IN('F','J'))  
  ,CONSTRAINT chk_FvrAtivo CHECK( FVR_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_FvrReg CHECK( FVR_REG IN('A','P','S')));
GO  
CREATE VIEW VFAVORECIDO AS
  SELECT FVR_CODIGO,FVR_NOME,FVR_APELIDO,FVR_BAIRRO,FVR_CEP
         ,FVR_CNPJCPF,FVR_CODCDD,FVR_DTCADASTRO,FVR_FISJUR,FVR_INSMUNIC
         ,FVR_CONTATO,FVR_ENDERECO,FVR_FONE,FVR_INS,FVR_CTAATIVO,FVR_CTAPASSIVO
         ,FVR_CADMUNIC,FVR_EMAIL,FVR_CODCTG,FVR_SENHA,FVR_COMPLEMENTO
         ,FVR_NUMERO,FVR_CODLGR,FVR_GCCP,FVR_GCCR,FVR_ATIVO,FVR_REG,FVR_CODUSR
    FROM FAVORECIDO  
CREATE TABLE dbo.BKPFAVORECIDO(
  FVR_ID INTEGER IDENTITY PRIMARY KEY NOT NULL 
  ,FVR_ACAO VARCHAR(1) NOT NULL
  ,FVR_DATA DATE DEFAULT GETDATE() NOT NULL
  ,FVR_CODIGO INTEGER NOT NULL
  ,FVR_NOME VARCHAR(60) NOT NULL  
  ,FVR_APELIDO VARCHAR(15) NOT NULL
  ,FVR_BAIRRO VARCHAR(15) NOT NULL
  ,FVR_CNPJCPF VARCHAR(14) NOT NULL
  ,FVR_CEP VARCHAR(8) NOT NULL  
  ,FVR_CODCDD VARCHAR(7) NOT NULL
  ,FVR_DTCADASTRO DATE DEFAULT GETDATE() NOT NULL
  ,FVR_FISJUR VARCHAR(1) NOT NULL
  ,FVR_INSMUNIC VARCHAR(20)
  ,FVR_CONTATO VARCHAR(40)
  ,FVR_ENDERECO VARCHAR(60)
  ,FVR_FONE VARCHAR(10)
  ,FVR_INS VARCHAR(19)
  ,FVR_CTAATIVO VARCHAR(15)
  ,FVR_CTAPASSIVO VARCHAR(15)
  ,FVR_CADMUNIC VARCHAR(20)
  ,FVR_EMAIL VARCHAR(60)
  ,FVR_CODCTG VARCHAR(3) NOT NULL
  ,FVR_SENHA VARCHAR(10)
  ,FVR_COMPLEMENTO VARCHAR(60)
  ,FVR_NUMERO VARCHAR(10) NOT NULL
  ,FVR_CODLGR VARCHAR(5) NOT NULL
  ,FVR_GCCP INTEGER NOT NULL
  ,FVR_GCCR INTEGER NOT NULL
  ,FVR_ATIVO VARCHAR(1) NOT NULL
  ,FVR_REG VARCHAR(1) NOT NULL
  ,FVR_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_bkpFvrAcao CHECK( FVR_ACAO IN('I','A','E'))  
);
    
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- FVR_CODIGO     | PK  |    |    | INT                |  Auto incremento
   -- FVR_NOME       |     |    |    | VC(60) NN          |
   -- FVR_APELIDO    |     |    |    | VC(15) NN          |     
   -- EMP_CNPJCPF    |     |    |    | VC(14) NN          |   
   -- FVR_CODCDD     | SEL |    |    | VC(7) NN           | Campo relacionado (CIDADE)   
   -- CDD_NOME       | SEL |    |    | VC(30) NN          | Campo relacionado (CIDADE) 
   -- FVR_DTCADASTRO |     |    |    | DAT NN             | Campo automatico   
   -- FVR_FISJUR     | CC  |    |    | VC(1) NN           | F|J  Se eh fisica ou juridica  
   -- FVR_INSMUNIC   |     |    |    | VC(20)             |
   -- FVR_CONTATO    |     |    |    | VC(40)             |   
   -- FVR_ENDERECO   |     |    |    | VC(60) NN          |
   -- FVR_NUMERO     |     |    |    | VC(10) NN          |   
   -- FVR_CEP        |     |    |    | VC(8) NN           |
   -- FVR_BAIRRO     |     |    |    | VC(15) NN          |   
   -- FVR_FONE       |     |    |    | VC(10) NN          |
   -- FVR_INS        |     |    |    | VC(19)             |   
   -- FVR_CTAATIVO   |     |    |    | VC(15) NN          | Gerado pelo trigger
   -- FVR_CTAPASSIVO |     |    |    | VC(15) NN          | Gerado pelo trigger
   -- FVR_CADMUNIC   |     |    |    | VC(20)             |   
   -- FVR_EMAIL      |     |    |    | VC(60)             |   
   -- FVR_CODCTG     | SEL |    |    | VC(3) NN           | Campo relacionado (CATEGORIA)   
   -- CTG_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (CATEGORIA) 
   -- FVR_SENHA      |     |    |    | VC(10)             |   
   -- FVR_COMPLEMENTO|     |    |    | VC(60)             |   
   -- FVR_CODLGR     | SEL |    |    | VC(5) NN           | Campo relacionado (LOGRADOURO)   
   -- LGR_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (LOGRADOURO)      
   -- FVR_GFCP       | SEL |    |    | INT NN             | Campo relacionado (GRUPOFAVORECIDO)   
   -- GF_NOME        | SEL |    |    | VC(40) NN          | Campo relacionado (GRUPOFAVORECIDO) 
   -- FVR_GFCR       | SEL |    |    | INT NN             | Campo relacionado (GRUPOFAVORECIDO)   
   -- GF_NOME        | SEL |    |    | VC(40) NN          | Campo relacionado (GRUPOFAVORECIDO) 
   -- FVR_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- FVR_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- FVR_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D05         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                                 F E R I A D O                                   --
--tblferiado
-------------------------------------------------------------------------------------
GO
CREATE TABLE FERIADO(
  FRD_DATA DATE NOT NULL
  ,FRD_CODEMP INTEGER NOT NULL
  ,FRD_NOME VARCHAR(30) NOT NULL
  ,FRD_ATIVO VARCHAR(1) NOT NULL
  ,FRD_REG VARCHAR(1) NOT NULL
  ,FRD_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_FrdAtivo CHECK( FRD_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_FrdReg CHECK( FRD_REG IN('A','P','S'))
  ,CONSTRAINT PKPFERIADO PRIMARY KEY (FRD_DATA, FRD_CODEMP));
GO
CREATE VIEW VFERIADO AS
  SELECT FRD_DATA
         ,FRD_CODEMP
         ,FRD_NOME
         ,FRD_ATIVO
         ,FRD_REG
         ,FRD_CODUSR
    FROM FERIADO
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS   |UPD |DEL | TIPO               | Obs
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------
   -- FRD_DATA       | PK   |    |    | DAT NN             |  
   -- FRD_CODEMP     |PK/SEL|    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL  |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- FRD_NOME       |      |    |    | VC(30) NN          |
   -- FRD_ATIVO      | CC   |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- FRD_REG        | FNC  |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- FRD_CODUSR     | OK   |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL  |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL  |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D19         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                                 F I L I A L                                     --
--tblfilial
-------------------------------------------------------------------------------------
GO
CREATE TABLE FILIAL(
  FLL_CODIGO INTEGER PRIMARY KEY NOT NULL
  ,FLL_NOME VARCHAR(40) NOT NULL
  ,FLL_APELIDO VARCHAR(15) NOT NULL
  ,FLL_BAIRRO VARCHAR(15) NOT NULL
  ,FLL_CEP VARCHAR(8) NOT NULL
  ,FLL_CNPJ VARCHAR(14) NOT NULL
  ,FLL_CODCDD VARCHAR(7) NOT NULL           --CIDADE
  ,FLL_CODLGR VARCHAR(5) NOT NULL           --LOGRADOURO
  ,FLL_ENDERECO VARCHAR(60) NOT NULL
  ,FLL_NUMERO VARCHAR(10) NOT NULL
  ,FLL_FONE VARCHAR(10)
  ,FLL_INS VARCHAR(19)
  ,FLL_INSCMUNIC VARCHAR(20)
  ,FLL_CODEMP INTEGER NOT NULL
  ,FLL_ATIVO VARCHAR(1) NOT NULL
  ,FLL_REG VARCHAR(1) NOT NULL
  ,FLL_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_FllCep CHECK( FLL_CEP LIKE('[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]'))      
  ,CONSTRAINT chk_FllCnpj CHECK( FLL_CNPJ NOT LIKE '%[^0-9]%' )          
  ,CONSTRAINT chk_FllAtivo CHECK( FLL_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_FllReg CHECK( FLL_REG IN('A','P','S')));
GO
CREATE VIEW VFILIAL AS
  SELECT FLL_CODIGO
         ,FLL_NOME
         ,FLL_APELIDO
         ,FLL_BAIRRO
         ,FLL_CEP
         ,FLL_CNPJ
         ,FLL_CODCDD
         ,FLL_CODLGR
         ,FLL_ENDERECO
         ,FLL_NUMERO
         ,FLL_FONE
         ,FLL_INS
         ,FLL_INSCMUNIC
         ,FLL_CODEMP
         ,FLL_ATIVO
         ,FLL_REG
         ,FLL_CODUSR
  FROM FILIAL
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- FLL_CODIGO     | PK  |    |    | INT                |  Gerado pelo trigger
   -- FLL_NOME       |     |    |    | VC(40) NN          |
   -- FLL_APELIDO    |     |    |    | VC(15) NN          |        
   -- FLL_CNPJ       |     |    |    | VC(14) NN          |   
   -- FLL_CODCDD     | SEL |    |    | VC(7) NN           | Campo relacionado (CIDADE)   
   -- CDD_NOME       | SEL |    |    | VC(30) NN          | Campo relacionado (CIDADE)      
   -- FLL_CODLGR     | SEL |    |    | VC(5) NN           | Campo relacionado (LOGRADOURO)   
   -- LGR_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (LOGRADOURO)      
   -- FLL_ENDERECO   |     |    |    | VC(60) NN          |
   -- FLL_NUMERO     |     |    |    | VC(10) NN          |   
   -- FLL_CEP        |     |    |    | VC(8) NN           |
   -- FLL_BAIRRO     |     |    |    | VC(15) NN          |   
   -- FLL_FONE       |     |    |    | VC(10) NN          |      
   -- FLL_INS        |     |    |    | VC(19)             |      
   -- FLL_INSCMUNIC  |     |    |    | VC(20)             |      
   -- FLL_CODEMP     | SEL |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- FLL_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- FLL_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- FLL_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D03         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--  
-------------------------------------------------------------------------------------
--                         F O R M A C O B R A N C A                               --
--tblformacobranca
-------------------------------------------------------------------------------------
CREATE TABLE FORMACOBRANCA(
  FC_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,FC_NOME VARCHAR(20) NOT NULL
  ,FC_ATIVO VARCHAR(1) NOT NULL
  ,FC_REG VARCHAR(1) NOT NULL
  ,FC_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_FcAtivo CHECK( FC_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_FcReg CHECK( FC_REG IN('A','P','S')));
GO
CREATE VIEW VFORMACOBRANCA AS
  SELECT FC_CODIGO,FC_NOME,FC_ATIVO,FC_REG,FC_CODUSR FROM FORMACOBRANCA
GO  
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('CAR','CARTEIRA'         ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('CS','COB SIMPLES'       ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('CD','COB DESCONTO'      ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('TRA','TRANSFERENCIA'    ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('BOL','BOLETO'           ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('CHE','CHEQUE'           ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('BOR','BORDERO'          ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('CRT','CARTORIO'         ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('DEP','DEPOSITO'         ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('DC','DEBITO EM CONTA'   ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('EF','EXTRA FINANCEIRO'  ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('SP','SISPAG'            ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('CAI','CAIXINHA'         ,'S'  ,'S'  ,1);
INSERT INTO dbo.VFORMACOBRANCA(FC_CODIGO,FC_NOME,FC_ATIVO ,FC_REG ,FC_CODUSR) VALUES('TED','TED'              ,'S'  ,'S'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- FC_CODIGO      | PK  |    |    | VC(03) NN          |
   -- FC_NOME        |     |    |    | VC(20) NN          |
   -- FC_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- FC_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- FC_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D20         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                         G R U P O F A V O R E C I D O                           --
--tblgrupofavorecido
-------------------------------------------------------------------------------------
GO
CREATE TABLE GRUPOFAVORECIDO(
  GF_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,GF_NOME VARCHAR(40) NOT NULL
  ,GF_ATIVO VARCHAR(1) NOT NULL
  ,GF_REG VARCHAR(1) NOT NULL
  ,GF_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_GfAtivo CHECK( GF_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_GfReg CHECK( GF_REG IN('A','P','S')));
GO
CREATE VIEW VGRUPOFAVORECIDO AS
  SELECT GF_CODIGO,GF_NOME,GF_ATIVO,GF_REG,GF_CODUSR FROM GRUPOFAVORECIDO
GO  
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('01','NAO SE APLICA'           ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('02','ASSINATURA'              ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('03','AGUA/LUZ'                ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('04','BANCOS'                  ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('05','BENEFICIOS'              ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('06','CLIENTES'                ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('07','DESPESA ADM'             ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('08','DIVERSOS'                ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('09','CELULAR'                 ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('10','COMERCIAL'               ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('11','CONSULTORIA'             ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('12','COPA E COZINHA'          ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('13','CORREIO'                 ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('14','DESPESAS'                ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('15','FRETE'                   ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('16','IMPOSTOS'                ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('17','INFORMATICA'             ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('18','INTERNET'                ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('19','IPVA'                    ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('20','MANUTENCAO PREDIAL'      ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('21','MAO DE OBRA TEMPORARIA'  ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('22','MATERIAL LIMPEZA'        ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('23','MATERIAL ESCRITORIO'     ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('24','OCUPACAO'                ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('25','PARCEIROS'               ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('26','PUBLICACOES'             ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('27','SALARIOS'                ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('28','SEG/LIMPEZA'             ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('29','SEGURO'                  ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('30','SINDICATO'               ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('31','SOCIOS'                  ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('32','TECNOLOGIA'              ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('33','TELEFONIA'               ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('34','UNIFORMES'               ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('35','VALE TRANSPORTE'         ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('36','VEICULOS'                ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('37','VIAGENS'                 ,'S'  ,'P'  ,1);
INSERT INTO dbo.VGRUPOFAVORECIDO(GF_CODIGO,GF_NOME,GF_ATIVO ,GF_REG ,GF_CODUSR) VALUES('38','ADVOGADOS'               ,'S'  ,'P'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- GF_CODIGO      | PK  |    |    | INT                |  Auto incremento
   -- GF_NOME        |     |    |    | VC(40) NN          |
   -- GF_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- GF_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- GF_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D11         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                I M P O S T O                                    --
-- Parametrizacao de impostos para NFP                                             -- 
--tblimposto
-------------------------------------------------------------------------------------
CREATE TABLE IMPOSTO(
  IMP_UFDE VARCHAR(3) NOT NULL            --ESTADO
  ,IMP_UFPARA VARCHAR(3) NOT NULL         --ESTADO
  ,IMP_CODNCM VARCHAR(10) NOT NULL        --NCM
  ,IMP_CODCTG VARCHAR(3) NOT NULL         --CATEGORIA
  ,IMP_ENTSAI VARCHAR(1) NOT NULL
  ,IMP_CODNO VARCHAR(2) NOT NULL          --NATUREZAOPERACAO
  ,IMP_CFOP VARCHAR(5) NOT NULL           --CFOP
  ,IMP_CSTICMS VARCHAR(3) NOT NULL
  ,IMP_ALIQICMS NUMERIC(6,2) NOT NULL
  ,IMP_REDUCAOBC NUMERIC(15,4) NOT NULL
  ,IMP_CSTIPI VARCHAR(3) NOT NULL
  ,IMP_ALIQIPI NUMERIC(6,2) NOT NULL
  ,IMP_CSTPIS VARCHAR(3) NOT NULL
  ,IMP_ALIQPIS NUMERIC(6,2) NOT NULL
  ,IMP_CSTCOFINS VARCHAR(3) NOT NULL
  ,IMP_ALIQCOFINS NUMERIC(6,2) NOT NULL
  ,IMP_ALIQST NUMERIC(6,2) NOT NULL
  ,IMP_ALTERANFP VARCHAR(1) NOT NULL
  ,IMP_CODEMP INTEGER NOT NULL
  ,IMP_CODFLL INTEGER NOT NULL   
  ,IMP_ATIVO VARCHAR(1) NOT NULL
  ,IMP_REG VARCHAR(1) NOT NULL
  ,IMP_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_ImpEntSai CHECK( IMP_ENTSAI IN('E','S'))  
  ,CONSTRAINT chk_ImpAlterarNfp CHECK( IMP_ALTERANFP IN('S','N'))  
  ,CONSTRAINT chk_ImpAtivo CHECK( IMP_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_ImpReg CHECK( IMP_REG IN('A','P','S'))
  ,CONSTRAINT PKIMPOSTO PRIMARY KEY (IMP_UFDE, IMP_UFPARA, IMP_CODNCM, IMP_CODCTG, IMP_ENTSAI, IMP_CODNO, IMP_CODEMP,IMP_CODFLL));
GO
CREATE VIEW VIMPOSTO AS
  SELECT IMP_UFDE
         ,IMP_UFPARA
         ,IMP_CODNCM
         ,IMP_CODCTG
         ,IMP_ENTSAI
         ,IMP_CODNO
         ,IMP_CFOP
         ,IMP_CSTICMS
         ,IMP_ALIQICMS
         ,IMP_REDUCAOBC
         ,IMP_CSTIPI
         ,IMP_ALIQIPI
         ,IMP_CSTPIS
         ,IMP_ALIQPIS
         ,IMP_CSTCOFINS
         ,IMP_ALIQCOFINS
         ,IMP_ALIQST
         ,IMP_ALTERANFP
         ,IMP_CODEMP
         ,IMP_CODFLL
         ,IMP_ATIVO
         ,IMP_REG
         ,IMP_CODUSR
    FROM IMPOSTO
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- IMP_UFDE       | SEL |    |    | VC(03) NN          | Campo relacionado (ESTADO)   
   -- EST_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (ESTADO)         
   -- IMP_UFPARA     | SEL |    |    | VC(03) NN          | Campo relacionado (ESTADO)   
   -- EST_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (ESTADO)         
   -- IMP_CODNCM     | SEL |    |    | VC(10) NN          | Campo relacionado (NCM)   
   -- NCM_NOME       | SEL |    |    | VC(60) NN          | Campo relacionado (ESTADO)   
   -- IMP_CODCTG     | SEL |    |    | VC(3) NN           | Campo relacionado (CATEGORIA)   
   -- CTG_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (CATEGORIA) 
   -- IMP_ENTSAI     | CC  |    |    | VC(1) NN           |  
   -- IMP_CODNO      |     |    |    | VC(2) NN           | Campo relacionado (NATUREZAOPERACAO)
   -- NO_NOME        | SEL |    |    | VC(30) NN          | Campo relacionado (NATUREZAOPERACAO)    
   -- IMP_CFOP       | SEL |    |    | VC(5) NN           | Campo relacionado (CFOP)      
   -- CFO_NOME       | SEL |    |    | VC(30) NN          | Campo relacionado (CFOP)       
   -- IMP_CSTICMS    | SEL |    |    | VC(3) NN           | Campo relacionado (CSTICMS)       
   -- ICMS_NOME      | SEL |    |    | VC(60) NN          | Campo relacionado (CSTICMS)   
   -- IMP_ALIQICMS   |     |    |    | NUM(6,2) NN        |
   -- IMP_REDUCAOBC  |     |    |    | NUM(15,4) NN       |
   -- IMP_CSTIPI     | SEL |    |    | VC(3) NN           | Campo relacionado (CSTIPI)
   -- IPI_NOME       | SEL |    |    | VC(60) NN          | Campo relacionado (CSTIPI)   
   -- IMP_ALIQIPI    |     |    |    | NUM(6,2) NN        |
   -- IMP_CSTPIS     | SEL |    |    | VC(3) NN           | Campo relacionado (CSTPIS)
   -- PIS_NOME       | SEL |    |    | VC(60) NN          | Campo relacionado (CSTPIS)   
   -- IMP_ALIQPIS    |     |    |    | NUM(6,2) NN        |
   -- IMP_CSTCOFINS  | SEL |    |    | VC(3) NN           | Campo relacionado (CSTPIS)   
   -- PIS_NOME       | SEL |    |    | VC(60) NN          | Campo relacionado (CSTPIS)   
   -- IMP_ALIQCOFINS |     |    |    | NUM(6,2) NN        |
   -- IMP_ALIQST     |     |    |    | NUM(6,2) NN        |
   -- IMP_ALTERARNFP | CC  |    |    | VC(1) NN           |  
   -- IMP_CODFLL     | SEL |    |    | INT NN             | Campo relacionado (FILIAL)
   -- FLL_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (FILIAL)        
   -- IMP_CODEMP     | SEL |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- IMP_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- IMP_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- IMP_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D23         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                             L O G R A D O U R O                                 --
--tbllogradouro
-------------------------------------------------------------------------------------
CREATE TABLE LOGRADOURO(
  LGR_CODIGO VARCHAR(5) PRIMARY KEY NOT NULL
  ,LGR_NOME VARCHAR(20) NOT NULL
  ,LGR_ATIVO VARCHAR(1) NOT NULL
  ,LGR_REG VARCHAR(1) NOT NULL
  ,LGR_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_LgrAtivo CHECK( LGR_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_LgrReg CHECK( LGR_REG IN('A','P','S'))
);
GO
CREATE VIEW VLOGRADOURO AS
  SELECT LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR FROM LOGRADOURO
GO  
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('AER'  ,'AEROPORTO'            ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('AL'   ,'ALAMEDA'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('AP'   ,'APARTAMENTO'          ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('AV'   ,'AVENIDA'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('BC'   ,'BECO SEM SAIDA MESMO' ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('BL'   ,'BLOCO'                ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('CAM'  ,'CAMINHO'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('ESCD' ,'ESCADINHA'            ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('EST'  ,'ESTACAO'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('ETR'  ,'ESTRADA'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('FAZ'  ,'FAZENDA'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('FORT' ,'FORTALEZA'            ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('GL'   ,'GALERIA'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('LD'   ,'LADEIRA'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('LGO'  ,'LARGO'                ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('PCA'  ,'PRACA'                ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('PRQ'  ,'PARQUE'               ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('PR'   ,'PRAIA'                ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('QD'   ,'QUADRA'               ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('KM'   ,'QUILOMETRO'           ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('QTA'  ,'QUINTA'               ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('ROD'  ,'RODOVIA'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('RUA'  ,'RUA'                  ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('SQD'  ,'SUPER QUADRA'         ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('TRV'  ,'TRAVESSA'             ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('VD'   ,'VIADUTO'              ,'S','P',1)
INSERT INTO dbo.VLOGRADOURO(LGR_CODIGO,LGR_NOME,LGR_ATIVO,LGR_REG,LGR_CODUSR) VALUES('VL'   ,'VILA'                 ,'S','P',1)
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- LGR_CODIGO     | PK  |    |    | VC(05) NN          |
   -- LGR_NOME       |     |    |    | VC(20) NN          |
   -- LGR_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- LGR_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- LGR_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D08         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                  M O E D A                                      --
--tblmoeda
-------------------------------------------------------------------------------------
CREATE TABLE MOEDA(
  MOE_CODIGO VARCHAR(4) PRIMARY KEY NOT NULL
  ,MOE_NOME VARCHAR(20) NOT NULL
  ,MOE_ATIVO VARCHAR(1) NOT NULL
  ,MOE_REG VARCHAR(1) NOT NULL
  ,MOE_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_MoeAtivo CHECK( MOE_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_MoeReg CHECK( MOE_REG IN('A','P','S')));
GO
CREATE VIEW VMOEDA AS
  SELECT MOE_CODIGO,MOE_NOME,MOE_ATIVO,MOE_REG,MOE_CODUSR FROM MOEDA
GO  
INSERT INTO dbo.VMOEDA(MOE_CODIGO,MOE_NOME,MOE_ATIVO,MOE_REG,MOE_CODUSR) VALUES('R$','REAL'   ,'S'  ,'P'  ,1);
INSERT INTO dbo.VMOEDA(MOE_CODIGO,MOE_NOME,MOE_ATIVO,MOE_REG,MOE_CODUSR) VALUES('US$','DOLAR' ,'S'  ,'P'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- MOE_CODIGO     | PK  |    |    | VC(04) NN          |
   -- MOE_NOME       |     |    |    | VC(20) NN          |
   -- MOE_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- MOE_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- MOE_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D08         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                       N A T U R E Z A O P E R A C A O                           --
--tblnaturezaoperacao
-------------------------------------------------------------------------------------
CREATE TABLE NATUREZAOPERACAO(
  NO_CODIGO VARCHAR(2) PRIMARY KEY NOT NULL
  ,NO_NOME VARCHAR(30) NOT NULL
  ,NO_ATIVO VARCHAR(1) NOT NULL
  ,NO_REG VARCHAR(1) NOT NULL
  ,NO_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_NoCodigo CHECK( NO_CODIGO LIKE('[0-9]'))  
  ,CONSTRAINT chk_NoAtivo CHECK( NO_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_NoReg CHECK( NO_REG IN('A','P','S')));
GO
CREATE VIEW VNATUREZAOPERACAO AS
  SELECT NO_CODIGO,NO_NOME,NO_ATIVO,NO_REG,NO_CODUSR FROM NATUREZAOPERACAO
GO
INSERT INTO VNATUREZAOPERACAO VALUES('1','VENDA'                  ,'S','S',1);
INSERT INTO VNATUREZAOPERACAO VALUES('2','DEVOLUCAO'              ,'S','S',1);
INSERT INTO VNATUREZAOPERACAO VALUES('3','TRANSFERENCIA'          ,'S','S',1);
INSERT INTO VNATUREZAOPERACAO VALUES('4','VENDA DE ATIVO'         ,'S','S',1);
INSERT INTO VNATUREZAOPERACAO VALUES('5','BRINDE'                 ,'S','S',1);
INSERT INTO VNATUREZAOPERACAO VALUES('6','DEMONSTRACAO'           ,'S','S',1);
INSERT INTO VNATUREZAOPERACAO VALUES('7','REMESSA PARA CONSERTO'  ,'S','S',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- NO_CODIGO      | PK  |    |    | VC(02) NN          |
   -- NO_NOME        |     |    |    | VC(30) NN          |
   -- NO_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- NO_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- NO_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D14         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                    N C M                                        --
--tblncm
-------------------------------------------------------------------------------------
CREATE TABLE NCM(
  NCM_CODIGO VARCHAR(10) PRIMARY KEY NOT NULL
  ,NCM_NOME VARCHAR(60) NOT NULL
  ,NCM_ATIVO VARCHAR(1) NOT NULL
  ,NCM_REG VARCHAR(1) NOT NULL
  ,NCM_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_NcmCodigo CHECK( NCM_CODIGO LIKE('[0-9][0-9][0-9][0-9][.][0-9][0-9][.][0-9][0-9]'))  
  ,CONSTRAINT chk_NcmAtivo CHECK( NCM_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_NcmReg CHECK( NCM_REG IN('A','P','S'))
);
GO
CREATE VIEW VNCM AS
  SELECT NCM_CODIGO,NCM_NOME,NCM_ATIVO,NCM_REG,NCM_CODUSR FROM NCM
GO
INSERT INTO VNCM VALUES('3404.90.29','CERA LIQUIDA'                             ,'S','P',1);
INSERT INTO VNCM VALUES('3405.40.00','SABOES, AGENTES ORGANICOS DE SUPERFICIE'  ,'S','P',1);
INSERT INTO VNCM VALUES('3819.00.00','FLUIDO DE FREIO'                          ,'S','P',1);
INSERT INTO VNCM VALUES('3820.00.00','ADITIVO RADIADOR'                         ,'S','P',1);
INSERT INTO VNCM VALUES('3824.90.29','CORRETOR LIQUIDO'                         ,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- NCM_CODIGO     | PK  |    |    | VC(10) NN          |
   -- NCM_NOME       |     |    |    | VC(60) NN          |
   -- NCM_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- NCM_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- NCM_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D14         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                               N F P R O D U T O                                 --
-- NFP_ENTSAI tem checar com SNF_ENTSAI                                            --
--tblnfproduto
-------------------------------------------------------------------------------------
GO
CREATE TABLE NFPRODUTO(
  NFP_NUMNF INTEGER NOT NULL
  ,NFP_CODSNF INTEGER NOT NULL            --SERIENF
  ,NFP_EMISSOR VARCHAR(14) NOT NULL
  ,NFP_CODNO VARCHAR(2) NOT NULL          --NATUREZAOPERACAO
  ,NFP_GUIA INTEGER NOT NULL
  ,NFP_VLRITENS NUMERIC(15,2) NOT NULL
  ,NFP_VLRFRETE NUMERIC(15,2) NOT NULL
  ,NFP_VLRSEGURO NUMERIC(15,2) NOT NULL
  ,NFP_VLROUTRAS NUMERIC(15,2) NOT NULL
  ,NFP_VLRIPI NUMERIC(15,2) NOT NULL
  ,NFP_VLRICMS NUMERIC(15,2) NOT NULL
  ,NFP_VLRST NUMERIC(15,2) NOT NULL
  ,NFP_VLRPIS NUMERIC(15,2) NOT NULL
  ,NFP_VLRCOFINS NUMERIC(15,2) NOT NULL
  ,NFP_VLRDESCONTO NUMERIC(15,2) NOT NULL
  ,NFP_VLRTOTAL NUMERIC(15,2) NOT NULL
  ,NFP_CODTRN INTEGER NOT NULL              --TRANSPORTADORA
  ,NFP_VOLUME VARCHAR(10)
  ,NFP_ESPECIE VARCHAR(10)
  ,NFP_CODVND INTEGER NOT NULL              --VENDEDOR
  ,NFP_DTCANCELA DATE
  ,NFP_CODCMP INTEGER NOT NULL              --COMPETENCIA
  ,NFP_LIVRO VARCHAR(1) NOT NULL
  ,NFP_DTENTRADA DATE NOT NULL
  ,NFP_PESOBRUTO NUMERIC(15,4) NOT NULL
  ,NFP_PESOLIQUIDO NUMERIC(15,4) NOT NULL
  ,NFP_RECIBONFE VARCHAR(20)
  ,NFP_CHAVENFE VARCHAR(50)
  ,NFP_CANCNFE VARCHAR(20)
  ,NFP_ENTSAI VARCHAR(1) NOT NULL
  --,NFP_XMLNF BLOB SUB_TYPE TEXT SEGMENT SIZE 80     --Naum entra na view
  ,NFP_ATIVO VARCHAR(1) NOT NULL
  ,NFP_REG VARCHAR(1) NOT NULL
  ,NFP_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_NfpLivro CHECK( NFP_LIVRO IN('S','N'))  
  ,CONSTRAINT chk_NfpEntSai CHECK( NFP_ENTSAI IN('E','S'))    
  ,CONSTRAINT chk_NfpAtivo CHECK( NFP_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_NfpReg CHECK( NFP_REG IN('A','P','S'))
  ,CONSTRAINT PKNFPRODUTO PRIMARY KEY(NFP_NUMNF,NFP_CODSNF));      
GO
CREATE VIEW VNFPRODUTO AS
  SELECT NFP_NUMNF
         ,NFP_CODSNF
         ,NFP_EMISSOR
         ,NFP_CODNO
         ,NFP_GUIA
         ,NFP_VLRITENS
         ,NFP_VLRFRETE
         ,NFP_VLRSEGURO
         ,NFP_VLROUTRAS
         ,NFP_VLRIPI
         ,NFP_VLRICMS
         ,NFP_VLRST
         ,NFP_VLRPIS
         ,NFP_VLRCOFINS
         ,NFP_VLRDESCONTO
         ,NFP_VLRTOTAL
         ,NFP_CODTRN
         ,NFP_VOLUME
         ,NFP_ESPECIE
         ,NFP_CODVND
         ,NFP_DTCANCELA
         ,NFP_CODCMP
         ,NFP_LIVRO
         ,NFP_DTENTRADA
         ,NFP_PESOBRUTO
         ,NFP_PESOLIQUIDO
         ,NFP_RECIBONFE
         ,NFP_CHAVENFE
         ,NFP_CANCNFE
         ,NFP_ENTSAI
         ,NFP_ATIVO
         ,NFP_REG
         ,NFP_CODUSR
    FROM NFPRODUTO
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS   |UPD |DEL | TIPO               | Obs
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------
   -- NFS_NUMNF      | PK   |    |    | INT NN             |  
   -- NFP_CODSNF     |SEL/PK|    |    | INT NN             | Campo relacionado (SERIENF)
   -- SNF_ENTSAI     | SEL  |    |    | VC(1) NN           | Campo relacionado (SERIENF)   
   -- NFP_EMISSOR    |      |    |    | VC(18) NN          |
   -- NFP_CODNO      | SEL  |    |    | VC(2) NN           | Campo relacionado (NATUREZAOPERACAO)
   -- NO_NOME        | SEL  |    |    | VC(30) NN          | Campo relacionado (NATUREZAOPERACAO)    
   -- NFP_GUIA       |      |    |    | INT NN             |      
   -- NFP_VLRITENS   |      |    |    | NUM(15,2) NN       |
   -- NFP_VLRFRETE   |      |    |    | NUM(15,2) NN       |
   -- NFP_VLRSEGURO  |      |    |    | NUM(15,2) NN       |
   -- NFP_VLROUTRAS  |      |    |    | NUM(15,2) NN       |
   -- NFP_VLRIPI     |      |    |    | NUM(15,2) NN       |
   -- NFP_VLRICMS    |      |    |    | NUM(15,2) NN       |
   -- NFP_VLRST      |      |    |    | NUM(15,2) NN       |
   -- NFP_VLRPIS     |      |    |    | NUM(15,2) NN       |
   -- NFP_VLRCOFINS  |      |    |    | NUM(15,2) NN       |
   -- NFP_VLRDESCONTO|      |    |    | NUM(15,2) NN       |
   -- NFP_VLRTOTAL   |      |    |    | NUM(15,2) NN       |
   -- NFP_CODTRN     | SEL  |    |    | INT NN             | Campo relacionado (TRANSPORTADORA)
   -- TRN_APELIDO    | SEL  |    |    | VC(15) NN          | Campo relacionado (TRANSPORTADORA)        
   -- NFP_VOLUME     |      |    |    | VC(10) NN          |   
   -- NFP_ESPECIE    |      |    |    | VC(10) NN          |   
   -- NFP_CODVND     | SEL  |    |    | INT NN             | Campo relacionado (VENDEDOR)
   -- VND_NOME       | SEL  |    |    | VC(40) NN          | Campo relacionado (VENDEDOR)        
   -- NFP_DTCANCELA  |      |    |    | DAT                |       
   -- NFP_CODCMP     | SEL  |    |    | INT NN             | Campo relacionado (COMPETENCIA)
   -- CMP_NOME       | SEL  |    |    | VC(6) NN           | Campo relacionado (COMPETENCIA)        
   -- NFP_LIVRO      | CC   |    |    | VC(1) NN           |
   -- NFP_DTENTRADA  |      |    |    | DAT NN             |   
   -- NFP_PESOBRUTO  |      |    |    | NUM(15,4) NN       |
   -- NFP_PESOLIQUIDO|      |    |    | NUM(15,4) NN       |
   -- NFP_RECIBONFE  |      |    |    | VC(20)             |
   -- NFP_CHAVENFE   |      |    |    | VC(50)             | 
   -- NFP_CANCNFE    |      |    |    | VC(20)             | 
   -- NFP_ENTSAI     | CC   |    |    | VC(1) NN           |  
   -- NFP_ATIVO      | CC   |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- NFP_REG        | FNC  |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- NFP_CODUSR     | OK   |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL  |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL  |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D26         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- -----------------------------------------------------------------------------------------------------------------
--
--    
-------------------------------------------------------------------------------------
--                               N F S E R V I C O                                 --
-- NFS_ENTSAI tem checar com SNF_ENTSAI                                            --
--tblnfservico
-------------------------------------------------------------------------------------
GO
CREATE TABLE NFSERVICO(
  NFS_NUMNF INTEGER NOT NULL
  ,NFS_CODSNF INTEGER NOT NULL              -- SERIENF
  ,NFS_GUIA INTEGER NOT NULL
  ,NFS_VLRTOTAL NUMERIC(15,2) NOT NULL
  ,NFS_VLRRETENCAO NUMERIC(15,2) NOT NULL
  ,NFS_DTCANCELA DATE
  ,NFS_CODCMP INTEGER NOT NULL              -- COMPETENCIA
  ,NFS_LIVRO VARCHAR(1) NOT NULL
  ,NFS_CODVND INTEGER NOT NULL              -- VENDEDOR
  ,NFS_LOTENFE INTEGER NOT NULL
  ,NFS_CODVERIFICACAO VARCHAR(50)
  ,NFS_NUMORIGEM INTEGER NOT NULL
  ,NFS_DTORIGEM DATE
  ,NFS_CODCDD VARCHAR(7) NOT NULL           -- CIDADE
  ,NFS_STATUSWS VARCHAR(200)
  ,NFS_CONTRATO INTEGER NOT NULL
  ,NFS_ENTSAI VARCHAR(1) NOT NULL  
  ,NFS_ATIVO VARCHAR(1) NOT NULL
  ,NFS_REG VARCHAR(1) NOT NULL
  ,NFS_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_NfsLivro CHECK( NFS_LIVRO IN('S','N'))  
  ,CONSTRAINT chk_NfsEntSai CHECK( NFS_ENTSAI IN('E','S'))    
  ,CONSTRAINT chk_NfsAtivo CHECK( NFS_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_NfsReg CHECK( NFS_REG IN('A','P','S'))
  ,CONSTRAINT PKNFSERVICO PRIMARY KEY(NFS_NUMNF,NFS_CODSNF));      
GO
CREATE VIEW VNFSERVICO AS
  SELECT NFS_NUMNF
         ,NFS_CODSNF
         ,NFS_GUIA
         ,NFS_VLRTOTAL
         ,NFS_VLRRETENCAO
         ,NFS_DTCANCELA
         ,NFS_CODCMP
         ,NFS_LIVRO
         ,NFS_CODVND
         ,NFS_LOTENFE
         ,NFS_CODVERIFICACAO
         ,NFS_NUMORIGEM
         ,NFS_DTORIGEM
         ,NFS_CODCDD
         ,NFS_STATUSWS
         ,NFS_CONTRATO
         ,NFS_ENTSAI
         ,NFS_ATIVO
         ,NFS_REG
         ,NFS_CODUSR
    FROM NFSERVICO
   -- -------------------|------|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO              |INS   |UPD |DEL | TIPO               | Obs
   -- -------------------|------|----|----|--------------------|----------------------------------------------------------
   -- NFS_NUMNF          | PK   |    |    | INT NN             |  
   -- NFS_CODSNF         |SEL/PK|    |    | INT NN             | Campo relacionado (SERIENF)
   -- SNF_ENTSAI         | SEL  |    |    | VC(1) NN           | Campo relacionado (SERIENF)   
   -- NFS_GUIA           |      |    |    | INT NN             |   
   -- NFS_VLRTOTAL       |      |    |    | NUM(15,2) NN       |
   -- NFS_VLRRETENCAO    |      |    |    | NUM(15,2) NN       |
   -- NFS_DTCANCELA      |      |    |    | DAT                |     
   -- NFS_CODCMP         | SEL  |    |    | INT NN             | Campo relacionado (COMPETENCIA)
   -- CMP_NOME           | SEL  |    |    | VC(6) NN           | Campo relacionado (COMPETENCIA)        
   -- NFS_LIVRO          | CC   |    |    | VC(1) NN           |  
   -- NFS_CODVND         | SEL  |    |    | INT NN             | Campo relacionado (VENDEDOR)
   -- VND_NOME           | SEL  |    |    | VC(40) NN          | Campo relacionado (VENDEDOR)        
   -- NFS_LOTENFE        |      |    |    | INT NN             |
   -- NFS_CODVERIFICACAO |      |    |    | VC(50)             |
   -- NFS_NUMORIGEM      |      |    |    | INT NN             |
   -- NFS_DTORIGEM       |      |    |    | DAT                |
   -- NFS_CODCDD         | SEL  |    |    | VC(7) NN           | Campo relacionado (CIDADE)   
   -- CDD_NOME           | SEL  |    |    | VC(30) NN          | Campo relacionado (CIDADE)      
   -- NFS_STATUSWS       |      |    |    | VC(200)            |
   -- NFS_CONTRATO       |      |    |    | INT NN             |
   -- NFS_ENTSAI         | CC   |    |    | VC(1) NN           |  
   -- NFS_ATIVO          | CC   |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- NFS_REG            | FNC  |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- NFS_CODUSR         | OK   |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO        | SEL  |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB         | SEL  |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D27             | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31             | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- -------------------|------|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- --------------------------------------------------------------------------------------------------------------------
--
--    
-------------------------------------------------------------------------------------
--                                 P A D R A O                                     --
--tblpadrao
-------------------------------------------------------------------------------------
GO
CREATE TABLE PADRAO(
  PDR_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,PDR_NOME VARCHAR(40) NOT NULL
  ,PDR_ATIVO VARCHAR(1) NOT NULL
  ,PDR_REG VARCHAR(1) NOT NULL
  ,PDR_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_PdrAtivo CHECK( PDR_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PdrReg CHECK( PDR_REG IN('A','P','S')));
GO
CREATE VIEW VPADRAO AS
  SELECT PDR_CODIGO,PDR_NOME,PDR_ATIVO,PDR_REG,PDR_CODUSR FROM PADRAO
GO  
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('1','COMPRA BENS'            ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('2','COMPRA MATERIAIS'       ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('3','CONCESSIONARIAS'        ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('4','EMPRESTIMOS EFETUADO'   ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('5','EMPRESTIMOS TOMADOS'    ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('6','ENTRADAS'               ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('7','FUNCIONARIOS'           ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('8','GUIA RECOLHIMENTO'      ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('9','JUDICIAS'               ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('10','LEASING/LOCACAO'       ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('11','OCUPACAO'              ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('12','OUTRAS'                ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('13','SINDICATOS'            ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('14','SOCIOS'                ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('15','TERCEIROS'             ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('16','TRANSPORTE'            ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('17','EMPRESTIMOS EFETUADO'  ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('18','EMPRESTIMOS TOMADOS'   ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('19','SAIDAS'                ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('20','NF VENDA PRODUTO'      ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('21','TARIFA BANCARIA'       ,'S','S',1);
INSERT INTO dbo.VPADRAO(PDR_CODIGO,PDR_NOME,PDR_ATIVO ,PDR_REG ,PDR_CODUSR) VALUES('23','COMERCIAIS'            ,'S','S',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- PDR_CODIGO     | PK  |    |    | INT                |  Auto incremento
   -- PDR_NOME       |     |    |    | VC(40) NN          |
   -- PDR_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PDR_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PDR_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D10         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             P A D R A O G R U P O                               --
--tblpadraogrupo
-------------------------------------------------------------------------------------
GO
CREATE TABLE PADRAOGRUPO(
  PG_CODPDR INTEGER NOT NULL         --PADRAO
  ,PG_CODPTP VARCHAR(2) NOT NULL     --PAGARTIPO  
  ,PG_ATIVO VARCHAR(1) NOT NULL
  ,PG_REG VARCHAR(1) NOT NULL
  ,PG_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_PgAtivo CHECK( PG_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PgReg CHECK( PG_REG IN('A','P','S'))
  ,CONSTRAINT PKPADRAOGRUPO PRIMARY KEY(PG_CODPDR,PG_CODPTP));      
GO
CREATE VIEW VPADRAOGRUPO AS
  SELECT PG_CODPDR,PG_CODPTP,PG_ATIVO,PG_REG,PG_CODUSR FROM PADRAOGRUPO
GO  
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('1' ,'CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('1' ,'PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('1' ,'MP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('2' ,'CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('2' ,'PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('2' ,'MP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('3' ,'CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('3' ,'PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('3' ,'MP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('4' ,'CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('4' ,'PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('4' ,'MP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('5' ,'CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('5' ,'PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('5' ,'MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('6' ,'CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('6' ,'PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('6' ,'MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('7' ,'CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('7' ,'PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('7' ,'MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('8' ,'CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('8' ,'PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('8' ,'MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('9' ,'CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('9' ,'PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('9' ,'MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('10','CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('10','PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('10','MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('11','CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('11','PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('11','MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('12','CP'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('12','PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('12','MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('13','CP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('13','PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('13','MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('14','CP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('14','PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('14','MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('15','CP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('15','PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('15','MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('16','CP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('16','PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('16','MP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('17','CR'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('17','PR'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('17','MR'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('18','CR'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('18','PR'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('18','MR'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('19','CR'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('19','PR'  ,'S','S',1);      
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('19','MR'  ,'S','S',1); 
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('20','CR'  ,'S','S',1); 
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('21','CP'  ,'S','S',1); 
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('22','CR'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('23','CP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('23','PP'  ,'S','S',1);
INSERT INTO dbo.VPADRAOGRUPO(PG_CODPDR,PG_CODPTP,PG_ATIVO ,PG_REG ,PG_CODUSR) VALUES('23','MP'  ,'S','S',1);      
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS   |UPD |DEL | TIPO               | Obs
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------
   -- PG_CODPDR      |PK/SEL|    |    | VC(15) NN          | Campo relacionado (PADRAO)
   -- PDR_NOME       |      |    |    | VC(40) NN          | Campo relacionado (PADRAO)   
   -- PG_CODPTP      |PK/SEL|    |    | VC(15) NN          | Campo relacionado (PAGARTIPO)
   -- PTP_NOME       | SEL  |    |    | VC(25) NN          | Campo relacionado (PAGARTIPO)   
   -- PG_ATIVO       | CC   |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PG_REG         | FNC  |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PG_CODUSR      | OK   |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL  |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL  |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D10         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|------|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             P A D R A O T I T U L O                             --
--tblpadraotitulo
-------------------------------------------------------------------------------------
GO
CREATE TABLE PADRAOTITULO(
  PT_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,PT_NOME VARCHAR(60) NOT NULL
  ,PT_CODTD VARCHAR(3) NOT NULL        --TIPODOCUMENTO  
  ,PT_CODFC VARCHAR(3) NOT NULL        --FORMACOBRANCA
  ,PT_DEBCRE VARCHAR(1) NOT NULL
  ,PT_CODCC VARCHAR(15) NOT NULL       --CONTACONTABIL
  ,PT_CODPDR INTEGER NOT NULL          --PADRAO
  ,PT_ATIVO VARCHAR(1) NOT NULL
  ,PT_REG VARCHAR(1) NOT NULL
  ,PT_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_PtDebCre CHECK( PT_DEBCRE IN('D','C'))  
  ,CONSTRAINT chk_PtAtivo CHECK( PT_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PtReg CHECK( PT_REG IN('A','P','S')));
GO
CREATE VIEW VPADRAOTITULO AS
  SELECT PT_CODIGO
         ,PT_NOME
         ,PT_CODTD
         ,PT_CODFC
         ,PT_DEBCRE
         ,PT_CODCC
         ,PT_CODPDR
         ,PT_ATIVO
         ,PT_REG
         ,PT_CODUSR
    FROM PADRAOTITULO
GO
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(1,'GASTOS COM EVENTOS'                               ,'NFP','BOL','D','4.01.01.01.0038','23','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(2,'GASTOS COM MARCAS E PATENTES'                     ,'NFP','BOL','D','4.01.01.01.0041','23','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(3,'GASTOS COM PROPAGANDA'                            ,'NFP','BOL','D','4.01.01.01.0040','23','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(4,'GASTOS COM VIAGENS'                               ,'NFP','BOL','D','4.01.01.01.0039','23','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(5,'COMPRA CARROS/MOTOS/CAMINHOES'                    ,'NFP','BOL','D','1.05.03.04.0001','1','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(6,'COMPRA COMPUT/IMPRESSORAS/PERIFERICOS'            ,'NFP','BOL','D','1.05.03.03.0001','1','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(7,'COMPRA IMOV/TERRENOS/CASAS/APTOS'                 ,'NFP','BOL','D','1.05.03.01.0001','1','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(8,'COMPRA MOVEIS E UTENSILIOS EM GERAL'              ,'NFP','BOL','D','1.05.03.05.0001','1','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(9,'COMPRA OUTROS TIPOS EQUIPS'                       ,'NFP','BOL','D','1.05.03.03.0002','1','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(10,'GASTOS COM BENS PEQ VALOR'                       ,'NFP','BOL','D','4.01.01.01.0046','1','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(11,'COMPRA MATERIAL INFORMATICA'                     ,'NFP','BOL','D','4.01.01.01.0010','2','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(12,'COMPRA IMPRESSOS DIVERSOS'                       ,'NFP','BOL','D','4.01.01.01.0011','2','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(13,'COMPRA MATERIAIS DIVERSOS'                       ,'NFP','BOL','D','4.01.01.01.0012','2','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(14,'COMPRA MATERIAL ESCRITORIO'                      ,'NFP','BOL','D','4.01.01.01.0009','2','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(15,'PGTO CONTA AGUA IMOV USADOS EMPRESA'             ,'NFP','BOL','D','4.01.01.01.0005','3','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(16,'PGTO CONTA ENERG ELETR IMOV USADOS EMPRESA'      ,'NFP','BOL','D','4.01.01.01.0004','3','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(17,'PGTO CONTA INTERNET LINK UTILIZADO EMPRESA'      ,'NFP','BOL','D','4.01.01.01.0007','3','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(18,'PGTO CONTA TELEF FIXA LINHAS UTILIZ EMPRESA'     ,'NFP','BOL','D','4.01.01.01.0007','3','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(19,'PGTO CONTA TELEF MOVEL APAREL UTILIZ EMPRESA'    ,'NFP','BOL','D','4.01.01.01.0007','3','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(20,'ENVIO ADTO PARA FORNECEDORES'                    ,'NFP','BOL','D','1.01.05.01.0001','4','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(21,'ENVIO EMPRESTIMOS PARA COLABORADORES'            ,'NFP','BOL','D','1.03.01.08.0001','4','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(22,'ENVIO EMPRESTIMOS PARA EMPRESAS DO GRUPO'        ,'NFP','BOL','D','1.03.01.08.0001','4','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(23,'ENVIO EMPRESTIMOS PARA OUTRAS EMPRESAS'          ,'NFP','BOL','D','1.03.01.08.0001','4','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(24,'ENVIO EMPRESTIMOS PARA SOCIOS'                   ,'NFP','BOL','D','1.03.01.08.0001','4','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(25,'ADTO PARA FUNCIONARIOS'                          ,'NFP','BOL','D','1.03.01.08.0001','4','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(26,'RETORNO ADTO PARA FORNECEDORES'                  ,'NFP','BOL','C','1.01.05.01.0001','17','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(27,'RETORNO EMPRESTIMOS EFETUADOS COLABORADORES'     ,'NFP','BOL','C','1.03.01.08.0001','17','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(28,'RETORNO EMPRESTIMOS EFETUADOS EMPRESAS GRUPO'    ,'NFP','BOL','C','1.03.01.08.0001','17','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(29,'RETORNO EMPRESTIMOS EFETUADOS OUTRAS EMPRESAS'   ,'NFP','BOL','C','1.03.01.08.0001','17','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(30,'RETORNO EMPRESTIMOS EFETUADOS SOCIOS'            ,'NFP','BOL','C','1.03.01.08.0001','17','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(31,'PGTO EMPRESTIMOS BANCARIOS'                      ,'NFP','BOL','D','2.01.01.03.0001','5','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(32,'PGTO EMPRESTIMOS EMPRESAS DO GRUPO'              ,'NFP','BOL','D','2.01.01.03.0001','5','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(33,'PGTO EMPRESTIMOS OUTRAS PESSOAS FISICAS'         ,'NFP','BOL','D','2.01.01.03.0001','5','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(34,'PGTO EMPRESTIMOS OUTRAS PESSOAS JURIDICAS'       ,'NFP','BOL','D','2.01.01.03.0001','5','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(35,'PGTO EMPRESTIMOS SOCIOS'                         ,'NFP','BOL','D','2.01.01.03.0001','5','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(36,'RECBTO EMPRESTIMOS BANCARIOS'                    ,'NFP','BOL','C','2.01.01.03.0001','18','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(37,'RECBTO EMPRESTIMOS EMPRESAS DO GRUPO'            ,'NFP','BOL','C','2.01.01.03.0001','18','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(38,'RECBTO EMPRESTIMOS OUTRAS PESSOAS FISICAS'       ,'NFP','BOL','C','2.01.01.03.0001','18','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(39,'RECBTO EMPRESTIMOS OUTRAS PESSOAS JURIDICAS'     ,'NFP','BOL','C','2.01.01.03.0001','18','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(40,'RECBTO EMPRESTIMOS SOCIOS'                       ,'NFP','BOL','C','2.01.01.03.0001','18','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(41,'DEVOL ADTO CLIENTES'                             ,'NFP','BOL','D','2.01.01.03.0002','6','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(42,'DEVOL COMISSAO VENDAS'                           ,'NFP','BOL','D','5.01.01.02.0003','6','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(43,'DEVOL INVESTIMENTO OUTRAS PESSOAS FISICAS'       ,'NFP','BOL','D','2.01.01.03.0001','6','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(44,'DEVOL INVESTIMENTO OUTRAS PESSOAS JURIDICAS'     ,'NFP','BOL','D','2.01.01.03.0001','6','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(45,'DEVOL VENDA EQUIPS INFORMATICA'                  ,'NFP','BOL','D','1.05.03.03.0001','6','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(46,'DEVOL VENDA IMOV'                                ,'NFP','BOL','D','1.05.03.01.0001','6','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(47,'DEVOL VENDA MOVEIS E UTENSILIOS'                 ,'NFP','BOL','D','1.05.03.05.0001','-1','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(48,'DEVOL VENDA OUTROS BENS DIVERSOS'                ,'NFP','BOL','D','1.05.03.03.0002','6','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(49,'DEVOL VENDA VEIC'                                ,'NFP','BOL','D','1.05.03.04.0001','6','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(50,'RECBTO ADTO CLIENTES'                            ,'NFP','BOL','C','2.01.01.03.0002','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(51,'RECBTO COMISSAO VENDAS'                          ,'NFP','BOL','C','5.01.01.02.0003','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(52,'RECBTO INVESTIM OUTRAS PESSOAS FISICAS'          ,'NFP','BOL','C','2.01.01.03.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(53,'RECBTO INVESTIM OUTRAS PESSOAS JURIDICAS'        ,'NFP','BOL','C','2.01.01.03.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(54,'RECBTO OUTROS VLRS/DIVERSOS'                     ,'NFP','BOL','C','5.01.01.01.0002','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(55,'RECBTO VENDA EQUIPS INFORMATICA'                 ,'NFP','BOL','C','1.05.03.03.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(56,'RECBTO VENDA IMOV'                               ,'NFP','BOL','C','1.05.03.01.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(57,'RECBTO VENDA MOVEIS E UTENSILIOS'                ,'NFP','BOL','C','1.05.03.05.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(58,'RECBTO VENDA OUTROS BENS DIVERSOS'               ,'NFP','BOL','C','1.05.03.03.0002','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(59,'RECBTO VENDA VEIC'                               ,'NFP','BOL','C','1.05.03.04.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(60,'13 SALARIO COLABORADORES REGISTRADOS'            ,'NFP','BOL','D','1.03.01.08.0004','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(61,'BOLSA AUXILIO MENSAL ESTAGIARIO'                 ,'NFP','BOL','D','1.03.01.08.0006','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(62,'CESTA BASICA DOS COLABORADORES'                  ,'NFP','BOL','D','1.03.01.08.0002','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(63,'FERIAS COLABORADORS REGISTRADOS'                 ,'NFP','BOL','D','1.03.01.08.0003','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(64,'OUTRAS DESPESAS COM COLABORADORES'               ,'NFP','BOL','D','1.03.01.08.0006','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(65,'OUTROS AUXILIOS OU VLRS PARA COLABORADORES'      ,'NFP','BOL','D','1.03.01.08.0006','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(66,'RESCISOES EX-COLABORADORES'                      ,'NFP','BOL','D','1.03.01.08.0006','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(67,'SALARIO MENSAL COLABORADOR REGISTRADO'           ,'NFP','BOL','D','1.03.01.08.0006','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(68,'VALE REFEICAO COLABORADORES'                     ,'NFP','BOL','D','1.03.01.08.0006','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(69,'VALE TRANSPORTE COLABORADORES'                   ,'NFP','BOL','D','1.03.01.08.0006','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(70,'PGTO MENSAL ASSISTENCIA MEDICA DA EMPRESA'       ,'NFP','BOL','D','1.03.01.08.0006','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(71,'PGTO MENSAL PLANO ODONTOLOGICO DA EMPRESA'       ,'NFP','BOL','D','1.03.01.08.0006','7','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(72,'GUIA ARREC ESTAD GARE-ICMS NORMAL'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(73,'GUIA ARREC ESTAD GARE-IPVA'                      ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(74,'GUIA ARREC ESTAD GARE-LICENC VEIC'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(75,'GUIA ARREC ESTAD GARE-MULTAS TRANSITO'           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(76,'GUIA NAC RECOLH ESTAD GNRE-ICMS DIF ALIQ'        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(77,'GUIA NAC RECOLH ESTAD GNRE-ICMS SUBST TRIB'      ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(78,'DARF CODIGO 0220-IMP RENDA'                      ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(79,'DARF CODIGO 0490-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(80,'DARF CODIGO 0561-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.03.01.08.0006','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(81,'DARF CODIGO 0588-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.03.01.08.0006','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(82,'DARF CODIGO 0916-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(83,'DARF CODIGO 0924-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(84,'DARF CODIGO 1599-IMP RENDA'                      ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(85,'DARF CODIGO 1708-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(86,'DARF CODIGO 2030-CONT. SOCIAL'                   ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(87,'DARF CODIGO 2063-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(88,'DARF CODIGO 2089-IMP RENDA'                      ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(89,'DARF CODIGO 2172-COFINS'                         ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(90,'DARF CODIGO 2319-IMP RENDA'                      ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(91,'DARF CODIGO 2362-IMP RENDA'                      ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(92,'DARF CODIGO 2372-CONT SOCIAL'                    ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(93,'DARF CODIGO 2469-CONT SOCIAL'                    ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(94,'DARF CODIGO 2484-CONT SOCIAL'                    ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(95,'DARF CODIGO 3208-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(96,'DARF CODIGO 3223-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(97,'DARF CODIGO 3277-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(98,'DARF CODIGO 3280-IMP RENDA RETIDO'               ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(99,'DARF CODIGO 3317-IMP RENDA'                      ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(100,'DARF CODIGO 3320-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(101,'DARF CODIGO 3373-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(102,'DARF CODIGO 3426-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(103,'DARF CODIGO 3703-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(104,'DARF CODIGO 4574-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(105,'DARF CODIGO 5204-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(106,'DARF CODIGO 5217-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(107,'DARF CODIGO 5273-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(108,'DARF CODIGO 5286-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(109,'DARF CODIGO 5299-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(110,'DARF CODIGO 5434-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(111,'DARF CODIGO 5442-COFINS'                        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(112,'DARF CODIGO 5602-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(113,'DARF CODIGO 5625-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(114,'DARF CODIGO 5629-COFINS'                        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(115,'DARF CODIGO 5706-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(116,'DARF CODIGO 5856-COFINS'                        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(117,'DARF CODIGO 5928-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(118,'DARF CODIGO 5936-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(119,'DARF CODIGO 5944-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(120,'DARF CODIGO 5952-PIS/COFINS/CONT. SOCIAL'       ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(121,'DARF CODIGO 5960-COFINS'                        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(122,'DARF CODIGO 5979-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(123,'DARF CODIGO 5987-CONT. SOCIAL'                  ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(124,'DARF CODIGO 5993-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(125,'DARF CODIGO 6012-CONT. SOCIAL'                  ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(126,'DARF CODIGO 6800-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(127,'DARF CODIGO 6813-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(128,'DARF CODIGO 6824-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(129,'DARF CODIGO 6840-COFINS'                        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(130,'DARF CODIGO 6891-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(131,'DARF CODIGO 6904-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(132,'DARF CODIGO 6912-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(133,'DARF CODIGO 7987-COFINS'                        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(134,'DARF CODIGO 8045-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(135,'DARF CODIGO 8053-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(136,'DARF CODIGO 8109-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(137,'DARF CODIGO 8301-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(138,'DARF CODIGO 8468-IMP. RENDA RETIDO'             ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(139,'DARF CODIGO 8496-PIS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(140,'DARF CODIGO 8645-COFINS'                        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(141,'DARF CODIGO 8673-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(142,'DARF CODIGO 9004-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(143,'DARF CODIGO 9017-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(144,'DARF CODIGO 9020-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(145,'DARF CODIGO 9025-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(146,'DARF CODIGO 9032-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(147,'DARF CODIGO 9058-IMP RENDA'                     ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(148,'DARF CODIGO 9100-PARCELAMENTO REFIS'            ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(149,'DARF CODIGO 9113-PARCELAMENTO REFIS'            ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(150,'DARF CODIGO 9126-PARCELAMENTO REFIS'            ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(151,'DARF CODIGO 9222-PARCELAMENTO REFIS'            ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(152,'DARF CODIGO 9385-IMP RENDA RETIDO'              ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(153,'DAS DO SIMPLES NAC-MENSAL'                      ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(154,'DAS DO SIMPLES NAC-PARCELAMENTO'                ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(155,'DOC ARREC MUNIC DAM-ISS FATURAMENTO'            ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(156,'DOC ARREC MUNIC DAM-ISS RETIDO TERCEIROS'       ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(157,'DOC ARREC MUNIC DAM-MULTAS TRANSITO'            ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(158,'DOC ARREC MUNIC DAM-TFA'                        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(159,'DOC ARREC MUNIC DAM-TFE'                        ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(160,'DOC ARREC MUNIC DAM-TRSS'                       ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(161,'FGTS CODIGO 115-FUNDO GARANTIA'                 ,'NFP','BOL','D','1.03.01.08.0006','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(162,'FGTS CODIGO 650-FUNDO GARANTIA'                 ,'NFP','BOL','D','1.03.01.08.0006','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(163,'GPS CODIGO 2003-INSS'                           ,'NFP','BOL','D','1.03.01.08.0006','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(164,'GPS CODIGO 2100-INSS'                           ,'NFP','BOL','D','1.03.01.08.0006','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(165,'GPS CODIGO 2119-INSS'                           ,'NFP','BOL','D','1.03.01.08.0006','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(166,'GPS CODIGO 2631-INSS'                           ,'NFP','BOL','D','1.01.05.05.0010','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(167,'DARF CODIGO 2991-INSS'                          ,'NFP','BOL','D','5.01.01.03.0006','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(168,'DARF CODIGO 2985-INSS'                          ,'NFP','BOL','D','5.01.01.03.0006','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(169,'PGTO CARNE IPTU IMOV USADOS EMPRESA'            ,'NFP','BOL','D','4.01.01.01.0003','8','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(170,'REMESSA DEPOSITOS JUDICIAS ACOES CIVEIS'        ,'NFP','BOL','D','1.03.01.04.0003','9','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(171,'REMESSA DEPOSITOS JUDICIAS ACOES FISCAIS'       ,'NFP','BOL','D','1.03.01.04.0001','9','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(172,'REMESSA DEPOSITOS JUDICIAS ACOES TRABAL'        ,'NFP','BOL','D','1.03.01.04.0002','9','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(173,'RETORNO ACOES JUDICIAIS CIVEIS'                 ,'NFP','BOL','C','1.03.01.04.0003','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(173,'RETORNO ACOES JUDICIAIS CIVEIS'                 ,'NFP','BOL','D','1.01.05.07.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(174,'RETORNO ACOES JUDICIAIS FISCAIS'                ,'NFP','BOL','C','1.03.01.04.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(174,'RETORNO ACOES JUDICIAIS FISCAIS'                ,'NFP','BOL','D','1.01.05.07.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(175,'RETORNO ACOES JUDICIAIS TRABALHISTAS'           ,'NFP','BOL','C','1.03.01.04.0002','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(175,'RETORNO ACOES JUDICIAIS TRABALHISTAS'           ,'NFP','BOL','D','1.01.05.07.0001','19','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(176,'LEASING EQUIPS'                                 ,'NFP','BOL','D','4.01.01.01.0054','10','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(177,'LEASING VEIC'                                   ,'NFP','BOL','D','4.01.01.01.0053','10','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(178,'LOCACAO EQUIPS'                                 ,'NFP','BOL','D','4.01.01.01.0051','10','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(179,'LOCACAO VEIC'                                   ,'NFP','BOL','D','4.01.01.01.0052','10','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(180,'PGTO MANUT CONSERTOS IMOV USADOS EMPRESA'       ,'NFP','BOL','D','4.01.01.01.0006','11','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(181,'OUTRAS CONTAS IMOV UTILIZ EMPRESA'              ,'NFP','BOL','D','4.01.01.01.0008','11','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(182,'PGTO ALUGUEL IMOV USADOS EMPRESA'               ,'NFP','BOL','D','4.01.01.01.0001','11','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(183,'PGTO CONDOMINIO IMOV USADOS EMPRESA'            ,'NFP','BOL','D','4.01.01.01.0002','11','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(184,'GASTOS REFORMAS EM IMOV TERCEIROS'              ,'NFP','BOL','D','1.05.03.09.0001','11','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(185,'GASTOS COM JORNAIS/REVISTAS/ASSINATURAS'        ,'NFP','BOL','D','4.01.01.01.0043','12','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(186,'GASTOS COM BRINDES E DOACOES'                   ,'NFP','BOL','D','4.01.01.01.0042','12','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(187,'GASTOS COM CARTORIOS'                           ,'NFP','BOL','D','4.01.01.01.0047','12','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(188,'GASTOS COM COPA E COZINHA'                      ,'NFP','BOL','D','4.01.01.01.0049','12','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(189,'GASTOS DIVERSOS ESCRITORIO'                     ,'NFP','BOL','D','4.01.01.01.0050','12','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(190,'GASTOS COM REFORMA EM BENS TERCEIROS'           ,'NFP','BOL','D','1.05.03.09.0002','12','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(191,'CONTRIBUICAO ASSISTENCIAL COLABORADORES'        ,'NFP','BOL','D','3.03.03.19.0005','13','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(192,'CONTRIBUICAO CONFEDERATIVA COLABORADORES'       ,'NFP','BOL','D','3.03.03.19.0005','13','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(193,'CONTRIBUICAO ODONTOLOGICA COLABORADORES'        ,'NFP','BOL','D','3.03.03.19.0005','13','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(194,'MENSALIDADE SINDICATOS FUNCION E PATRONAL'      ,'NFP','BOL','D','3.03.03.19.0005','13','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(195,'TAXA NEGOCIAL SINDICATOS'                       ,'NFP','BOL','D','3.03.03.19.0005','13','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(196,'PGTO DO PROLABORE DOS SOCIOS'                   ,'NFP','BOL','D','3.03.03.03.0001','14','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(197,'PGTO MENSAL SOCIOS OU CONTAS DOS MESMOS'        ,'NFP','BOL','D','2.05.03.01.0002','14','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(198,'COMISSOES VENDEDORES'                           ,'NFP','BOL','D','5.01.01.03.0008','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(199,'SERVICOS TERCEIROS DIVERSOS'                    ,'NFP','BOL','D','4.01.01.01.0021','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(200,'SERVICOS ASSESSORIA CONTABIL'                   ,'NFP','BOL','D','4.01.01.01.0014','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(201,'SERVICOS ASSESSORIA DEPTO PESSOAL'              ,'NFP','BOL','D','4.01.01.01.0017','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(202,'SERVICOS ASSESSORIA INFORMATICA'                ,'NFP','BOL','D','4.01.01.01.0015','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(203,'SERVICOS ASSESSORIA FINANCEIRA'                 ,'NFP','BOL','D','4.01.01.01.0016','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(204,'SERVICOS ASSESSORIA JURIDICA'                   ,'NFP','BOL','D','4.01.01.01.0013','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(205,'SERVICOS ASSESSORIAS DIVERSAS'                  ,'NFP','BOL','D','4.01.01.01.0018','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(206,'SERVICOS MANUT EQUIPS'                          ,'NFP','BOL','D','4.01.01.01.0020','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(207,'SERVICOS MOTOBOY E FRETES'                      ,'NFP','BOL','D','4.01.01.01.0019','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(208,'SERVICOS HOSTING/SERVIDORES'                    ,'NFP','BOL','D','4.01.01.01.0022','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(209,'SERVICOS EMAILS EXTERNOS/CLOUND'                ,'NFP','BOL','D','4.01.01.01.0023','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(210,'SERVICOS FERRAMENTAS BUSCA WEB'                 ,'NFP','BOL','D','4.01.01.01.0024','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(211,'SERVICOS ANUNCIOS E MARKETING PERIODICOS'       ,'NFP','BOL','D','4.01.01.01.0025','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(212,'SERVICOS ANUNCIOS E MARKETING WEB'              ,'NFP','BOL','D','4.01.01.01.0026','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(213,'SERVICOS ANUNCIOS E MARKETING MIDIA'            ,'NFP','BOL','D','4.01.01.01.0027','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(214,'SERVICOS ASSESSORIA IMPRENSA'                   ,'NFP','BOL','D','4.01.01.01.0028','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(215,'SERVICOS REGISTRO MARCAS/PATENTES'              ,'NFP','BOL','D','4.01.01.01.0029','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(216,'COMISSOES PORTAIS VENDAS WEB'                   ,'NFP','BOL','D','5.01.01.03.0009','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(217,'COMISSOES REPRESENTANTES COMERCIAIS'            ,'NFP','BOL','D','5.01.01.03.0010','15','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(218,'GASTOS VEIC EXCETO MANUT/COMPRA/LOCACAO'        ,'NFP','BOL','D','4.01.01.01.0021','16','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(219,'GASTOS COMBUSTIVEL'                             ,'NFP','BOL','D','4.01.01.01.0044','16','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(220,'GASTOS PEDAGIOS'                                ,'NFP','BOL','D','4.01.01.01.0045','16','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(221,'BIS RATEIO'                                     ,'NFP','BOL','D','4.01.01.01.0045','12','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(222,'VENDA'                                          ,'NFP','BOL','D','5.01.01.02.0002','20','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(223,'DEVOL'                                          ,'NFP','BOL','D','5.01.01.02.0002','20','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(224,'TRANSFERENCIA'                                  ,'NFP','BOL','D','5.01.01.02.0002','20','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(225,'VENDA ATIVO'                                    ,'NFP','BOL','D','5.01.01.02.0002','20','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(226,'BRINDE'                                         ,'NFP','BOL','D','5.01.01.02.0002','20','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(227,'DEMONSTRACAO'                                   ,'NFP','BOL','D','5.01.01.02.0002','20','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(228,'REMESSA PARA CONSERTO'                          ,'NFP','BOL','D','5.01.01.02.0002','20','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(229,'DESPESA CPMF'                                   ,'TAR','DC' ,'D','4.01.01.01.0055','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(230,'DESPESA TARIFAS MENSAIS'                        ,'TAR','DC' ,'D','4.01.01.01.0056','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(231,'DESPESAS EVENTUAIS'                             ,'TAR','DC' ,'D','4.01.01.01.0057','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(232,'ENTRADAS EVENTUAIS'                             ,'TAR','DC' ,'D','4.01.01.01.0058','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(233,'PERDA EM OPERACAO CAMBIO'                       ,'TAR','DC' ,'D','4.01.01.01.0060','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(234,'OUTRAS DESPESAS COM OPERACOES CAMBIO'           ,'TAR','DC' ,'D','4.01.01.01.0061','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(235,'JUROS PAGO USO CAPITAL GIRO'                    ,'TAR','DC' ,'D','4.01.01.01.0066','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(236,'JUROS PAGO EMPRESTIMOS BANCARIOS'               ,'TAR','DC' ,'D','4.01.01.01.0067','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(237,'JUROS PAGO USO CAPITAL CTA GARANTIDA'           ,'TAR','DC' ,'D','4.01.01.01.0068','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(238,'JUROS PAGO EMPRESTIMOS TERCEIROS'               ,'TAR','DC' ,'D','4.01.01.01.0069','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(239,'JUROS PAGO EMPRESTIMOS EMPRESAS DO GRUPO'       ,'TAR','DC' ,'D','4.01.01.01.0070','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(240,'JUROS PAGO EMPRESTIMOS SOCIOS/ADM'              ,'TAR','DC' ,'D','4.01.01.01.0071','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(241,'JUROS PAGOS EM OUTRAS OPERACOES'                ,'TAR','DC' ,'D','4.01.01.01.0072','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(242,'OUTRAS DESPESAS COM OPERACOES FINANC'           ,'TAR','DC' ,'D','4.01.01.01.0073','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(243,'TAXAS OPERADORAS-CARTAO DEBITO'                 ,'TAR','DC' ,'D','4.01.01.01.0074','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(244,'TAXAS OPERADORAS-CARTAO CREDITO'                ,'TAR','DC' ,'D','4.01.01.01.0075','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(245,'TAXAS OPERADORAS-PAYPAL'                        ,'TAR','DC' ,'D','4.01.01.01.0076','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(246,'TAXAS BANCOS-CUSTODIA E EMISSAO BOLETOS'        ,'TAR','DC' ,'D','4.01.01.01.0077','21','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(247,'GANHO EM OPERACAO CAMBIO'                       ,'TAR','DC' ,'C','4.01.01.01.0059','22','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(248,'JUROS RECEBIDO APLICACOES FINANC BANCARIAS'     ,'TAR','DC' ,'C','4.01.01.01.0062','22','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(249,'JUROS RECEBIDO EMPRESTIMOS TERCEIROS'           ,'TAR','DC' ,'C','4.01.01.01.0063','22','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(250,'JUROS RECEBIDO EMPRESTIMOS EMPRESAS DO GRUPO'   ,'TAR','DC' ,'C','4.01.01.01.0064','22','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(251,'JUROS RECEBIDO OUTRAS OPERACOES'                ,'TAR','DC' ,'C','4.01.01.01.0065','22','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(252,'OUTROS CRED CONTA NAO RELAC ANTERIORMENTE'      ,'TAR','DC' ,'C','5.01.01.01.0002','22','S','P',1);
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(253,'ADTO FORNECEDORES'                              ,'REC','DEP','D','1.01.05.01.0001','6','S','P',1);  
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(254,'ADTO CLIENTES'                                  ,'REC','DEP','C','2.01.01.03.0002','19','S','P',1);    
INSERT INTO VPADRAOTITULO(PT_CODIGO,PT_NOME,PT_CODTD,PT_CODFC,PT_DEBCRE,PT_CODCC,PT_CODPDR,PT_ATIVO,PT_REG,PT_CODUSR) VALUES(255,'DEVOL ADTOTO FORNECEDORES'                      ,'REC','DEP','C','1.01.05.01.0001','19','S','P',1);  
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- PT_CODIGO      | PK  |    |    | INT                |  Auto incremento
   -- PT_NOME        |     |    |    | VC(60) NN          |
   -- PT_CODTD       | SEL |    |    | VC(3) NN           | Campo relacionado (TIPODOCUMENTO)   
   -- TD_NOME        | SEL |    |    | VC(20) NN          | Campo relacionado (TIPODOCUMENTO)      
   -- PT_CODFC       | SEL |    |    | VC(3) NN           | Campo relacionado (FORMACOBRANCA)   
   -- FC_NOME        | SEL |    |    | VC(20) NN          | Campo relacionado (FORMACOBRANCA)      
   -- PT_DEBCRE      | CC  |    |    | VC(1) NN           |  
   -- PT_CODCC       | SEL |    |    | VC(15) NN          | Campo relacionado (CONTAGERENCIAL)
   -- CC_NOME        | SEL |    |    | VC(40) NN          | Campo relacionado (CONTAGERENCIAL)   
   -- PT_CODPDR      | SEL |    |    | VC(15) NN          | Campo relacionado (PADRAO)
   -- PDR_NOME       | SEL |    |    | VC(40) NN          | Campo relacionado (PADRAO)   
   -- PT_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PT_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PT_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D10         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                   P A G A R                                     --
-- Tabela multi-empresa                                                            --
--tblpagar
-------------------------------------------------------------------------------------
GO
CREATE TABLE PAGAR(
  PGR_GUIA INTEGER NOT NULL
  ,PGR_BLOQUEADO VARCHAR(1) NOT NULL
  ,PGR_CHEQUE VARCHAR(10)
  ,PGR_CODBNC INTEGER NOT NULL          --BANCO
  ,PGR_CODFVR INTEGER NOT NULL          --FAVORECIDO
  ,PGR_CODFC VARCHAR(3) NOT NULL           --FORMACOBRANCA
  ,PGR_CODTD VARCHAR(3) NOT NULL           --TIPODOCUMENTO
  ,PGR_VENCTO DATE NOT NULL  
  ,PGR_DATAPAGA DATE
  ,PGR_DOCTO VARCHAR(12) NOT NULL
  ,PGR_DTDOCTO DATE NOT NULL
  ,PGR_CODPTT VARCHAR(1) NOT NULL       --PAGARTITULO
  ,PGR_GUIAREF INTEGER NOT NULL
  ,PGR_OBSERVACAO VARCHAR(120) NOT NULL
  ,PGR_NUMPAR INTEGER NOT NULL  
  ,PGR_PARCELA INTEGER NOT NULL
  ,PGR_CODPTP VARCHAR(2) NOT NULL       --PAGARTIPO
  ,PGR_CODPT INTEGER NOT NULL           --PADRAOTITULO
  ,PGR_VLRDESCONTO NUMERIC(15,2)
  ,PGR_VLREVENTO NUMERIC(15,2)  DEFAULT 0 NOT NULL
  ,PGR_VLRLIQUIDO NUMERIC(15,2)  DEFAULT 0 NOT NULL
  ,PGR_VLRMULTA NUMERIC(15,2)
  ,PGR_VLRRETENCAO NUMERIC(15,2)  DEFAULT 0 NOT NULL
  ,PGR_VLRPIS NUMERIC(15,4) NOT NULL
  ,PGR_VLRCOFINS NUMERIC(15,4) NOT NULL
  ,PGR_VLRCSLL NUMERIC(15,4) NOT NULL
  ,PGR_CODFVRCC VARCHAR(15) NOT NULL        --CONTACONTABIL(CONTRAPARTIDA FAVORECIDO)
  ,PGR_CODBNCCC VARCHAR(15) NOT NULL        --CONTACONTABIL(CONTRAPARTIDA BANCO)
  ,PGR_CODSNF INTEGER NOT NULL              --SERIENF
  ,PGR_DTMOVTO DATE DEFAULT GETDATE() NOT NULL
  ,PGR_APR VARCHAR(1) NOT NULL
  ,PGR_CODEMP INTEGER NOT NULL              --Somente para facilitar os filtros  
  ,PGR_CODFLL INTEGER NOT NULL              --Somente para facilitar os filtros
  ,PGR_CODLAY INTEGER NOT NULL
  ,PGR_LOTECNAB INTEGER NOT NULL
  ,PGR_VERDIREITO VARCHAR(1) NOT NULL       --Como vem de NFP/NFS/CONTRATO aqui informo se vou olhar direito de usuario
  ,PGR_ATIVO VARCHAR(1) NOT NULL
  ,PGR_REG VARCHAR(1) NOT NULL
  ,PGR_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_PgrNumPar CHECK( PGR_NUMPAR > 0 )
  ,CONSTRAINT chk_PgrParcela CHECK( PGR_PARCELA > 0 )  
  ,CONSTRAINT chk_PgrBloqueado CHECK( PGR_BLOQUEADO IN('S','N'))    
  ,CONSTRAINT chk_PgrVerDireito CHECK( PGR_VERDIREITO IN('S','N'))    
  ,CONSTRAINT chk_PgrApr CHECK( PGR_APR IN('S','N'))    
  ,CONSTRAINT chk_PgrAtivo CHECK( PGR_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PgrReg CHECK( PGR_REG IN('A','P','S')));
GO
CREATE VIEW VPAGAR AS
  SELECT PGR_GUIA
         ,PGR_BLOQUEADO
         ,PGR_CHEQUE
         ,PGR_CODBNC
         ,PGR_CODFVR
         ,PGR_CODFC
         ,PGR_CODTD
         ,PGR_VENCTO
         ,PGR_DATAPAGA
         ,PGR_DOCTO
         ,PGR_DTDOCTO
         ,PGR_CODPTT
         ,PGR_GUIAREF
         ,PGR_OBSERVACAO
         ,PGR_NUMPAR
         ,PGR_PARCELA
         ,PGR_CODPTP
         ,PGR_VLRDESCONTO
         ,PGR_VLREVENTO
         ,PGR_VLRLIQUIDO
         ,PGR_VLRMULTA
         ,PGR_VLRRETENC
         ,PGR_VLRPIS
         ,PGR_VLRCOFINS
         ,PGR_VLRCSLL
         ,PGR_CODFVRCC
         ,PGR_CODBNCCC
         ,PGR_CODSNF
         ,PGR_DTMOVTO
         ,PGR_APR
         ,PGR_CODEMP
         ,PGR_CODFLL
         ,PGR_CODLAY
         ,PGR_LOTECNAB
         ,PGR_ATIVO
         ,PGR_REG
         ,PGR_CODUSR
    FROM PAGAR
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- PGR_GUIA       | PK  |    |    | INT NN             |
   -- PGR_BLOQUEADO  | CC  |    |    | VC(1) NN           |  
   -- PGR_CHEQUE     |     |    |    | VC(10)             |
   -- PGR_CODBNC     | SEL |    |    | INT NN             | Campo relacionado (BANCO)
   -- BNC_NOME       | SEL |    |    | VC(40) NN          | Campo relacionado (BANCO)   
   -- PGR_CODFVR     | SEL |    |    | INT NN             | Campo relacionado (FAVORECIDO)
   -- FVR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (FAVORECIDO)   
   -- PGR_CODFC      | SEL |    |    | VC(3) NN           | Campo relacionado (FORMACOBRANCA)   
   -- FC_NOME        | SEL |    |    | VC(20) NN          | Campo relacionado (FORMACOBRANCA)   
   -- PGR_CODTD      | SEL |    |    | VC(3) NN           | Campo relacionado (TIPODOCUMENTO)   
   -- TD_NOME        | SEL |    |    | VC(20) NN          | Campo relacionado (TIPODOCUMENTO)      
   -- PGR_VENCTO     |     |    |    | DAT NN             |
   -- PGR_DATAPAGA   |     |    |    | DAT                |   
   -- PGR_DOCTO      |     |    |    | VC(12) NN          |  
   -- PGR_DTDOCTO    |     |    |    | DAT NN             |  
   -- PGR_CODPTT     | SEL |    |    | VC(15) NN          | Campo relacionado (PAGARTITULO)
   -- PTT_NOME       | SEL |    |    | VC(25) NN          | Campo relacionado (PAGARTITULO)   
   -- PGR_GUIAREF    |     |    |    | INT NN             |   
   -- PGR_OBSERVACAO |     |    |    | VC(120) NN         |         
   -- PGR_NUMPAR     |     |    |    | INT NN             |     
   -- PGR_PARCELA    |     |    |    | INT NN             |      
   -- PGR_CODPTP     | SEL |    |    | VC(15) NN          | Campo relacionado (PAGARTIPO)
   -- PTP_NOME       | SEL |    |    | VC(25) NN          | Campo relacionado (PAGARTIPO)   
   -- PGR_VLRDESCONTO|     |    |    | NUM(15,2) NN       |
   -- PGR_VLREVENTO  |     |    |    | NUM(15,2) NN       |
   -- PGR_VLRLIQUIDO |     |    |    | NUM(15,2) NN       |
   -- PGR_VLRMULTA   |     |    |    | NUM(15,2) NN       |
   -- PGR_VLRRETENC  |     |    |    | NUM(15,2) NN       |
   -- PGR_VLRPIS     |     |    |    | NUM(15,2) NN       |
   -- PGR_VLRCOFINS  |     |    |    | NUM(15,2) NN       |
   -- PGR_VLRCSLL    |     |    |    | NUM(15,2) NN       |
   -- PGR_CODFVRCC   |     |    |    | VC(15) NN          | CONTACONTABIL(CONTRAPARTIDA FAVORECIDO)
   -- PGR_CODBNCCC   |     |    |    | VC(15) NN          | CONTACONTABIL(CONTRAPARTIDA BANCO)
   -- PGR_CODSNF     | SEL |    |    | INT NN             | Campo relacionado (SERIENF)
   -- SNF_ENTSAI     | SEL |    |    | VC(1) NN           | Campo relacionado (SERIENF)   
   -- PGR_DTMOVTO    | DEF |    |    | DAT NN             | Automatico pelo default  
   -- PGR_APR        | CC  |    |    | VC(1) NN           |  
   -- PGR_CODFLL     |     |    |    | INT NN             |Somente para facilitar os filtros
   -- PGR_CODLAY     |     |    |    | INT NN             |
   -- PGR_LOTECNAB   |     |    |    | INT NN             |
   -- PGR_VERDIREITO | CC  |    |    | VC(1) NN           | Como vem de NFP/NFS/CONTRATO aqui informo se vou olhar direito de usuario 
   -- PGR_CODEMP     | SEL |    |    | INT NN             | Campo relacionado (EMPRESA) - Somente para facilitar os filtros 
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- PGR_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PGR_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PGR_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D28         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                           P A G A R T I T U L O                                 --
--tblpagartitulo
-------------------------------------------------------------------------------------
GO
CREATE TABLE PAGARTITULO(
  PTT_CODIGO VARCHAR(1) PRIMARY KEY NOT NULL
  ,PTT_NOME VARCHAR(25) NOT NULL
  ,PTT_ATIVO VARCHAR(1) NOT NULL
  ,PTT_REG VARCHAR(1) NOT NULL
  ,PTT_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_PttCodigo CHECK( PTT_CODIGO LIKE('[A-Z]'))  
  ,CONSTRAINT chk_PttAtivo CHECK( PTT_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PttReg CHECK( PTT_REG IN('A','P','S')));
GO
CREATE VIEW VPAGARTITULO AS
  SELECT PTT_CODIGO,PTT_NOME,PTT_ATIVO,PTT_REG,PTT_CODUSR FROM PAGARTITULO
GO
INSERT INTO VPAGARTITULO VALUES('B','BORDERO'                   ,'S','S',1);
INSERT INTO VPAGARTITULO VALUES('C','CONTRATO'                  ,'S','S',1);
INSERT INTO VPAGARTITULO VALUES('E','LANCAMENTO EXTRA'          ,'S','S',1);
INSERT INTO VPAGARTITULO VALUES('F','FATURAMENTO'               ,'S','S',1);
INSERT INTO VPAGARTITULO VALUES('L','CADASTRO FINANCEIRO'       ,'S','S',1);
INSERT INTO VPAGARTITULO VALUES('O','DEPOSITO NAO IDENTIFICADO' ,'S','S',1);
INSERT INTO VPAGARTITULO VALUES('R','RECIBO'                    ,'S','S',1);
INSERT INTO VPAGARTITULO VALUES('N','TARIFA'                    ,'S','S',1);
INSERT INTO VPAGARTITULO VALUES('T','TRANSFERENCIA'             ,'S','S',1);
INSERT INTO VPAGARTITULO VALUES('D','DESMEMBRAMENTO'            ,'S','S',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- PTT_CODIGO     | PK  |    |    | VC(01) NN          |
   -- PTT_NOME       |     |    |    | VC(25) NN          |
   -- PTT_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PTT_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PTT_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D25         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                           P A G A R T I P O                                     --
--tblpagartipo
-------------------------------------------------------------------------------------
GO
CREATE TABLE PAGARTIPO(
  PTP_CODIGO VARCHAR(1) PRIMARY KEY NOT NULL
  ,PTP_NOME VARCHAR(25) NOT NULL
  ,PTP_VALOR INTEGER NOT NULL  
  ,PTP_CNAB VARCHAR(1) NOT NULL     --Se aceita pagamento/recebimento por cnab
  ,PTP_ATIVO VARCHAR(1) NOT NULL
  ,PTP_REG VARCHAR(1) NOT NULL
  ,PTP_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_PtpCodigo CHECK( PTP_CODIGO LIKE('[0-9]'))  
  ,CONSTRAINT chk_PtpCnab CHECK( PTP_CNAB IN('S','N'))  
  ,CONSTRAINT chk_PtpAtivo CHECK( PTP_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PtpReg CHECK( PTP_REG IN('A','P','S')));
GO
CREATE VIEW VPAGARTIPO AS
  SELECT PTP_CODIGO,PTP_NOME,PTP_ATIVO,PTP_REG,PTP_CODUSR FROM PAGARTIPO
GO
INSERT INTO VPAGARTPO VALUES('CP','CONTAS A PAGAR'        ,-1 ,'S','S','S',1);
INSERT INTO VPAGARTPO VALUES('CR','CONTAS A RECEBER'      ,1  ,'S','S','S',1);
INSERT INTO VPAGARTPO VALUES('DT','DESCONTO TOTAL'        ,0  ,'N','S','S',1);
INSERT INTO VPAGARTPO VALUES('EX','TITULO EXCLUIDO'       ,0  ,'N','S','S',1);
INSERT INTO VPAGARTPO VALUES('LE','LANCTO EXTRA'          ,1  ,'N','S','S',1);
INSERT INTO VPAGARTPO VALUES('OP','ORDEM DE PRODUCAO'     ,1  ,'N','S','P',1);
INSERT INTO VPAGARTPO VALUES('PP','PROVISAORIO A PAGAR'   ,-1 ,'N','S','P',1);
INSERT INTO VPAGARTPO VALUES('PR','PROVISAORIO A RECEBER' ,1  ,'N','S','S',1);
INSERT INTO VPAGARTPO VALUES('MP','MENSAL A PAGAR'        ,-1 ,'N','S','S',1);
INSERT INTO VPAGARTPO VALUES('MR','MENSAL A RECEBER'      ,1  ,'N','S','S',1);
INSERT INTO VPAGARTPO VALUES('RQ','REQUISICAO'            ,1  ,'N','S','S',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- PTP_CODIGO     | PK  |    |    | VC(01) NN          |
   -- PTP_NOME       |     |    |    | VC(25) NN          |
   -- PTP_VALOR      |     |    |    | INT NN             | CP/PP/PR -1 senao 1
   -- PTP_CNAB       | CC  |    |    | VC(1) NN           | Se aceita pagamento/recebimento por cnab 
   -- PTP_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PTP_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PTP_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D25         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                P A I S                                          --
--tblpais
-------------------------------------------------------------------------------------
GO
CREATE TABLE PAIS(
  PAI_CODIGO INTEGER PRIMARY KEY NOT NULL
  ,PAI_NOME VARCHAR(30) NOT NULL
  ,PAI_DDI INTEGER DEFAULT 0 NOT NULL  
  ,PAI_ATIVO VARCHAR(1) NOT NULL
  ,PAI_REG VARCHAR(1) NOT NULL
  ,PAI_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_PaiDdi CHECK( PAI_DDI>=0 )  
  ,CONSTRAINT chk_PaiAtivo CHECK( PAI_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PaiReg CHECK( PAI_REG IN('A','P','S'))
);
GO
CREATE VIEW VPAIS AS
  SELECT PAI_CODIGO,PAI_NOME,PAI_DDI,PAI_ATIVO,PAI_REG,PAI_CODUSR FROM PAIS
GO
INSERT INTO VPAIS( PAI_CODIGO,PAI_NOME,PAI_DDI,PAI_ATIVO ,PAI_REG ,PAI_CODUSR) VALUES(1058,'BRASIL'   ,55,'S','S',1);   
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- PAI_CODIGO     | PK  |    |    | INT                | Campo informado
   -- PAI_NOME       |     |    |    | VC(30) NN          |
   -- PAI_DDI        |     |    |    | INT NN             | 
   -- PAI_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PAI_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PAI_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D08         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             P R O D U T O
--tblproduto
-------------------------------------------------------------------------------------
GO
CREATE TABLE PRODUTO(
  PRD_CODIGO VARCHAR(15) NOT NULL
  ,PRD_CODEMP INTEGER NOT NULL
  ,PRD_NOME VARCHAR(60) NOT NULL
  ,PRD_CODNCM VARCHAR(10) NOT NULL             --NCM
  ,PRD_ST VARCHAR(1) NOT NULL
  ,PRD_ALIQICMS NUMERIC(6,2) NOT NULL
  ,PRD_REDUCAOBC NUMERIC(6,2) NOT NULL
  ,PRD_IPI VARCHAR(1) NOT NULL
  ,PRD_ALIQIPI NUMERIC(6,2) NOT NULL
  ,PRD_CSTIPI VARCHAR(3) NOT NULL             --CSTIPI
  ,PRD_CODEMB VARCHAR(3) NOT NULL             --EMBALAGEM
  ,PRD_VLRVENDA NUMERIC(15,4) NOT NULL
  ,PRD_CODPO VARCHAR(1) NOT NULL              --PRODUTOORIGEM
  ,PRD_CODBARRAS VARCHAR(20)
  ,PRD_PESOBRUTO NUMERIC(15,4) NOT NULL
  ,PRD_PESOLIQUIDO NUMERIC(15,4) NOT NULL
  ,PRD_DTCADASTRO DATE DEFAULT GETDATE() NOT NULL
  ,PRD_ATIVO VARCHAR(1) NOT NULL
  ,PRD_REG VARCHAR(1) NOT NULL
  ,PRD_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_PrdSt CHECK( PRD_ST IN('S','N'))  
  ,CONSTRAINT chk_PrdIpi CHECK( PRD_IPI IN('S','N'))  
  ,CONSTRAINT chk_PrdAtivo CHECK( PRD_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PrdReg CHECK( PRD_REG IN('A','P','S'))
  ,CONSTRAINT PKPRODUTO PRIMARY KEY (PRD_CODIGO,PRD_CODEMP));  
GO
CREATE VIEW VPRODUTO AS
  SELECT PRD_CODIGO
         ,PRD_CODEMP
         ,PRD_NOME
         ,PRD_CODNCM
         ,PRD_ST
         ,PRD_ALIQICMS
         ,PRD_REDUCAOBC
         ,PRD_IPI
         ,PRD_ALIQIPI
         ,PRD_CSTIPI
         ,PRD_CODEMB
         ,PRD_VLRVENDA
         ,PRD_CODPO
         ,PRD_CODBARRAS
         ,PRD_PESOBRUTO
         ,PRD_PESOLIQUIDO
         ,PRD_DTCADASTRO
         ,PRD_ATIVO
         ,PRD_REG
         ,PRD_CODUSR
   FROM PRODUTO;
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- PRD_CODIGO     | PK  |    |    | VC(15) NN          |
   -- PRD_CODEMP     | PK  |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- PRD_NOME       |     |    |    | VC(60) NN          |
   -- PRO_CODNCM     | SEL |    |    | VC(10) NN          | Campo relacionado (NCM)   
   -- NCM_NOME       | SEL |    |    | VC(60) NN          | Campo relacionado (ESTADO)   
   -- PRD_ST         | CC  |    |    | VC(1) NN           |  
   -- PRD_ALIQICMS   |     |    |    | NUM(6,2) NN        |
   -- PRD_REDUCAOBC  |     |    |    | NUM(6,2) NN        |
   -- PRD_IPI        | CC  |    |    | VC(1) NN           |  
   -- PRD_ALIQIPI    |     |    |    | NUM(6,2) NN        |
   -- PRD_CSTIPI     | SEL |    |    | VC(3) NN           | Campo relacionado (CSTIPI)
   -- IPI_NOME       | SEL |    |    | VC(60) NN          | Campo relacionado (CSTIPI)   
   -- PRD_CODEMB     | SEL |    |    | VC(3) NN           | Campo relacionado (EMBALAGEM)   
   -- EMB_NOME       | SEL |    |    | VC(30) NN          | Campo relacionado (EMBALAGEM)      
   -- PRD_VLRVENDA   |     |    |    | NUM(15,4) NN       |
   -- PRD_CODEMB     | SEL |    |    | VC(1) NN           | Campo relacionado (PRODUTOORIGEM)   
   -- EMB_NOME       | SEL |    |    | VC(30) NN          | Campo relacionado (PRODUTOORIGEM)      
   -- PRD_CODBARRAS  |     |    |    | VC(20)             |
   -- PRD_PESOBRUTO  |     |    |    | NUM(15,4) NN       |
   -- PRD_PESOLIQUIDO|     |    |    | NUM(15,4) NN       |
   -- PRD_DTCADASTRO |     |    |    | DAT NN             | Automatico pelo default
   -- PRD_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PRD_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PRD_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D09         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--   
-------------------------------------------------------------------------------------
--                            P R O D U T O O R I G E M                            --
--tblprodutoorigem
-------------------------------------------------------------------------------------
CREATE TABLE PRODUTOORIGEM(
  PO_CODIGO VARCHAR(1) PRIMARY KEY NOT NULL
  ,PO_NOME VARCHAR(30) NOT NULL
  ,PO_ATIVO VARCHAR(1) NOT NULL
  ,PO_REG VARCHAR(1) NOT NULL
  ,PO_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_PoCodigo CHECK( PO_CODIGO LIKE('[0-9]'))  
  ,CONSTRAINT chk_PoAtivo CHECK( PO_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_PoReg CHECK( PO_REG IN('A','P','S')));
GO
CREATE VIEW VPRODUTOORIGEM AS
  SELECT PO_CODIGO,PO_NOME,PO_ATIVO,PO_REG,PO_CODUSR FROM PRODUTOORIGEM
GO
INSERT INTO VPRODUTOORIGEM VALUES('0','NACIONAL'                ,'S','S',1);
INSERT INTO VPRODUTOORIGEM VALUES('1','IMPORTACAO DIRETA'       ,'S','S',1);
INSERT INTO VPRODUTOORIGEM VALUES('2','IMPORTADO MERC INTERNO'  ,'S','S',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- PO_CODIGO      | PK  |    |    | VC(01) NN          |
   -- PO_NOME        |     |    |    | VC(30) NN          |
   -- PO_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- PO_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- PO_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D09         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--   
-------------------------------------------------------------------------------------
--                         Q U A L I F I C A C A O C O N T                         --
--tblqualificacaocont
-------------------------------------------------------------------------------------
CREATE TABLE QUALIFICACAOCONT(
  QC_CODIGO VARCHAR(4) PRIMARY KEY NOT NULL
  ,QC_NOME VARCHAR(70) NOT NULL
  ,QC_ATIVO VARCHAR(1) NOT NULL
  ,QC_REG VARCHAR(1) NOT NULL
  ,QC_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_QcAtivo CHECK( QC_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_QcReg CHECK( QC_REG IN('A','P','S')));
GO
CREATE VIEW VQUALIFICACAOCONT AS
  SELECT QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR FROM QUALIFICACAOCONT
GO
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('001','SIGNATARIO DA ECD COM E-CNPJ OU E-PJ','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('203','DIRETOR','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('204','CONSELHEIRO DE ADMINISTRACAO','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('205','ADMINISTRADOR','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('206','ADMINISTRADOR DO GRUPO','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('207','ADMINISTRADOR DE SOCIEDADE FILIADA','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('220','ADMINISTRADOR JUDICIAL PF','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('222','ADMINISTRADOR JUDICIAL PJ PROFISSIONAL RESP','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('223','ADMINISTRADOR JUDICIAL/GESTOR','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('226','GESTOR JUDICIAL','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('309','PROCURADOR','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('312','INVENTARIANTE','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('313','LIQUIDANTE','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('315','INTERVENTOR','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('801','EMPRESARIO','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('900','CONTADOR/CONTABILISTA','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('910','CONTADOR/CONTABILISTA RESP TERMO VERIFIC P/ FINS DE SUBSTIT ECD','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('920','AUDITOR INDEPENDENTE RESP TERMO VERIFIC P/ FINS SUBSTIT ECD','S','P',1);
INSERT INTO VQUALIFICACAOCONT(QC_CODIGO,QC_NOME,QC_ATIVO,QC_REG,QC_CODUSR) VALUES('999','OUTROS','S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- QC_CODIGO      | PK  |    |    | VC(04) NN          |
   -- QC_NOME        |     |    |    | VC(70) NN          |
   -- QC_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- QC_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- QC_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D03         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                R 1 0 F A V O R E C I D O                        --
-- antida R01TCLIENTE                                                              --
-- Guarda o ultimo servico faturado para este cliente                              --
--tblr10
-------------------------------------------------------------------------------------
CREATE TABLE R01FAVORECIDO(
  R01_CODFVR INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,R01_CODSRV INTEGER NOT NULL
  ,R01_GUIA INTEGER NOT NULL);
-------------------------------------------------------------------------------------
--                                R 0 2 C O N T R A T O                            --
--tblr02
-------------------------------------------------------------------------------------
GO
CREATE TABLE R02CONTRATO(
  R02_CODCON INTEGER NOT NULL
  ,R02_ITEM INTEGER NOT NULL
  ,R02_CODSRV INTEGER NOT NULL            --SERVICO
  ,R02_VENCTO DATE NOT NULL
  ,R02_VALOR NUMERIC(15,2) NOT NULL
  ,R02_GUIA INTEGER NOT NULL
  ,R02_OBSERVACAO VARCHAR(120) NOT NULL  
  ,R02_TABR03ITEM VARCHAR(1) NOT NULL
  ,R02_FATURADO VARCHAR(1) NOT NULL
  ,R02_ATIVO VARCHAR(1) NOT NULL
  ,R02_REG VARCHAR(1) NOT NULL
  ,R02_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_R02Faturado CHECK( R02_FATURADO IN('S','N'))  
  ,CONSTRAINT chk_R02Ativo CHECK( R02_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_R02Reg CHECK( R02_REG IN('A','P','S'))
  ,CONSTRAINT PKR02TCONTRATO PRIMARY KEY (R02_CODCON, R02_ITEM));
GO
CREATE VIEW VR02CONTRATO AS
  SELECT R02_CODCON
         ,R02_ITEM
         ,R02_CODSRV
         ,R02_VENCTO
         ,R02_VALOR
         ,R02_GUIA
         ,R02_OBSERVACAO
         ,R02_TABR03ITEM
         ,R02_FATURADO
         ,R02_ATIVO
         ,R02_REG
         ,R02_CODUSR
    FROM R02CONTRATO;
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- R02_CODCON     | PK  |    |    | INT NN             |
   -- R02_ITEM       | PK  |    |    | INT NN             |
   -- R02_CODSRV     | SEL |    |    | INT NN             | Campo relacionado (SERVICO)   
   -- SRV_NOME       | SEL |    |    | VC(60) NN          | Campo relacionado (SERVICO)      
   -- R02_VENCTO     |     |    |    | DAT NN             |
   -- R02_VALOR      |     |    |    | NUM(15,2) NN       |
   -- R02_GUIA       |     |    |    | INT NN             |  
   -- R02_OBSERVACAO |     |    |    | VC(120) NN         |      
   -- R02_TABR03ITEM |     |    |    | VC(1) NN           | Automatico pelo trigger sem necessidade de constraint   
   -- R02_FATURADO   | CC  |    |    | VC(1) NN           |    
   -- R02_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- R02_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- R02_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D15         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                                R 0 3 C O N T R A T O                            --
-- Somente para controlar qual novo item na alteracao                              --
--tblr03
-------------------------------------------------------------------------------------
GO
CREATE TABLE R03CONTRATO(
  R03_CODCON INTEGER PRIMARY KEY NOT NULL
  ,R03_ITEM INTEGER NOT NULL);
-------------------------------------------------------------------------------------
--                                R 0 7 N F P R O D U T O                          --
--tblr07
-------------------------------------------------------------------------------------
GO
CREATE TABLE R07NFPRODUTO(
  R07_GUIA INTEGER NOT NULL
  ,R07_ITEM INTEGER NOT NULL
  ,R07_CODPRD VARCHAR(15) NOT NULL                --PRODUTO
  ,R07_CFOP VARCHAR(5) NOT NULL                   --CFOP
  ,R07_VLRUNITARIO NUMERIC(15,2) NOT NULL
  ,R07_UNIDADES NUMERIC(15,4) NOT NULL
  ,R07_VLRITEM NUMERIC(15,2) NOT NULL
  ,R07_CODEMB VARCHAR(3) NOT NULL                 --EMBALAGEM
  ,R07_VLRFRETE NUMERIC(15,2) NOT NULL
  ,R07_VLRSEGURO NUMERIC(15,2) NOT NULL
  ,R07_VLROUTRAS NUMERIC(15,2) NOT NULL
  ,R07_VLRDESCONTO NUMERIC(15,2) NOT NULL
  ,R07_CSTICMS VARCHAR(3) NOT NULL                --CSTICMS
  ,R07_ALIQICMS NUMERIC(15,2) NOT NULL
  ,R07_REDUCAOBC NUMERIC(15,2) NOT NULL
  ,R07_BCICMS NUMERIC(15,2) NOT NULL
  ,R07_VLRICMS NUMERIC(15,2) NOT NULL
  ,R07_VLRICMSISENTAS NUMERIC(15,2) NOT NULL
  ,R07_VLRICMSOUTRAS NUMERIC(15,2) NOT NULL
  ,R07_CSTIPI VARCHAR(3) NOT NULL                 --CSTIPI
  ,R07_ALIQIPI NUMERIC(15,2) NOT NULL
  ,R07_BCIPI NUMERIC(15,2) NOT NULL
  ,R07_VLRIPI NUMERIC(15,2) NOT NULL
  ,R07_VLRIPIISENTAS NUMERIC(15,2) NOT NULL
  ,R07_VLRIPIOUTRAS NUMERIC(15,2) NOT NULL
  ,R07_CSTPIS VARCHAR(3) NOT NULL                 --CSTPIS
  ,R07_ALIQPIS NUMERIC(15,2) NOT NULL
  ,R07_BCPIS NUMERIC(15,2) NOT NULL
  ,R07_VLRPIS NUMERIC(15,2) NOT NULL
  ,R07_CSTCOFINS VARCHAR(3) NOT NULL              --CSTPIS
  ,R07_ALIQCOFINS NUMERIC(15,2) NOT NULL
  ,R07_BCCOFINS NUMERIC(15,2) NOT NULL
  ,R07_VLRCOFINS NUMERIC(15,2) NOT NULL
  ,R07_ALIQST NUMERIC(15,2) NOT NULL
  ,R07_BCST NUMERIC(15,2) NOT NULL
  ,R07_VLRST NUMERIC(15,2) NOT NULL
  ,R07_TOTALITEM NUMERIC(15,2) NOT NULL
  ,R07_ENTSAI VARCHAR(1) NOT NULL                 --somente para facilitar estoque
  ,R07_ATIVO VARCHAR(1) NOT NULL
  ,R07_REG VARCHAR(1) NOT NULL
  ,R07_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_R07EntSai CHECK( R07_ENTSAI IN('E','S'))  
  ,CONSTRAINT chk_R07Ativo CHECK( R07_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_R07Reg CHECK( R07_REG IN('A','P','S'))
  ,CONSTRAINT PKR07NFPRODUTO PRIMARY KEY (R07_GUIA, R07_ITEM));
GO
CREATE VIEW VR07NFPRODUTO AS
  SELECT R07_GUIA
         ,R07_ITEM
         ,R07_CODPRD
         ,R07_CFOP
         ,R07_VLRUNITARIO
         ,R07_UNIDADES
         ,R07_VLRITEM
         ,R07_CODEMB
         ,R07_VLRFRETE
         ,R07_VLRSEGURO
         ,R07_VLROUTRAS
         ,R07_VLRDESCONTO
         ,R07_CSTICMS
         ,R07_ALIQICMS
         ,R07_REDUCAOBC
         ,R07_BCICMS
         ,R07_VLRICMS
         ,R07_VLRICMSISENTAS
         ,R07_VLRICMSOUTRAS
         ,R07_CSTIPI
         ,R07_ALIQIPI
         ,R07_BCIPI
         ,R07_VLRIPI
         ,R07_VLRIPIISENTAS
         ,R07_VLRIPIOUTRAS
         ,R07_CSTPIS
         ,R07_ALIQPIS
         ,R07_BCPIS
         ,R07_VLRPIS
         ,R07_CSTCOFINS
         ,R07_ALIQCOFINS
         ,R07_BCCOFINS
         ,R07_VLRCOFINS
         ,R07_ALIQST
         ,R07_BCST
         ,R07_VLRST
         ,R07_TOTALITEM
         ,R07_ENTSAI
         ,R07_ATIVO
         ,R07_REG
         ,R07_CODUSR
    FROM R07NFPRODUTO;
CREATE TABLE R07NFPRODUTO(
   -- ------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO             |INS  |UPD |DEL | TIPO               | Obs
   -- ------------------|-----|----|----|--------------------|----------------------------------------------------------
   -- R07_GUIA          | PK  |    |    | INT NN             |
   -- R07_ITEM          | PK  |    |    | INT NN             |
   -- R07_CODPRD        | SEL |    |    | INT NN             | Campo relacionado (PRODUTO)   
   -- PRD_NOME          | SEL |    |    | VC(60) NN          | Campo relacionado (PRODUTO)      
   -- R07_CFOP          | SEL |    |    | VC(5) NN           | Campo relacionado (CFOP)      
   -- CFO_NOME          | SEL |    |    | VC(30) NN          | Campo relacionado (CFOP)       
   -- R07_VLRUNITARIO   |     |    |    | NUM(15,2) NN       |
   -- R07_UNIDADES      |     |    |    | NUM(15,4) NN       |
   -- R07_VLRITEM       |     |    |    | NUM(15,2) NN       |
   -- R07_CODEMB        | SEL |    |    | VC(3) NN           | Campo relacionado (EMBALAGEM)   
   -- EMB_NOME          | SEL |    |    | VC(30) NN          | Campo relacionado (EMBALAGEM)      
   -- R07_VLRFRETE      |     |    |    | NUM(15,2) NN       |
   -- R07_VLRSEGURO     |     |    |    | NUM(15,2) NN       |
   -- R07_VLROUTRAS     |     |    |    | NUM(15,2) NN       |
   -- R07_VLRDESCONTO   |     |    |    | NUM(15,2) NN       |
   -- R07_CSTICMS       | SEL |    |    | VC(3) NN           | Campo relacionado (CSTICMS)       
   -- ICMS_NOME         | SEL |    |    | VC(60) NN          | Campo relacionado (CSTICMS)   
   -- R07_ALIQICMS      |     |    |    | NUM(15,2) NN       |
   -- R07_REDUCAOBC     |     |    |    | NUM(15,2) NN       |
   -- R07_BCICMS        |     |    |    | NUM(15,2) NN       |
   -- R07_VLRICMS       |     |    |    | NUM(15,2) NN       |
   -- R07_VLRICMSISENTAS|     |    |    | NUM(15,2) NN       |
   -- R07_VLRICMSOUTRAS |     |    |    | NUM(15,2) NN       |
   -- R07_CSTIPI        | SEL |    |    | VC(3) NN           | Campo relacionado (CSTIPI)
   -- IPI_NOME          | SEL |    |    | VC(60) NN          | Campo relacionado (CSTIPI)   
   -- R07_ALIQIPI       |     |    |    | NUM(15,2) NN       |
   -- R07_BCIPI         |     |    |    | NUM(15,2) NN       |
   -- R07_VLRIPI        |     |    |    | NUM(15,2) NN       |
   -- R07_VLRIPIISENTAS |     |    |    | NUM(15,2) NN       |
   -- R07_VLRIPIOUTRAS  |     |    |    | NUM(15,2) NN       |
   -- R07_CSTPIS        | SEL |    |    | VC(3) NN           | Campo relacionado (CSTPIS)
   -- PIS_NOME          | SEL |    |    | VC(60) NN          | Campo relacionado (CSTPIS)   
   -- R07_ALIQPIS       |     |    |    | NUM(15,2) NN       |
   -- R07_BCPIS         |     |    |    | NUM(15,2) NN       |
   -- R07_VLRPIS        |     |    |    | NUM(15,2) NN       |
   -- R07_CSTCOFINS     | SEL |    |    | VC(3) NN           | Campo relacionado (CSTPIS)  
   -- R07_ALIQCOFINS    |     |    |    | NUM(15,2) NN       |
   -- R07_BCCOFINS      |     |    |    | NUM(15,2) NN       |
   -- R07_VLRCOFINS     |     |    |    | NUM(15,2) NN       |
   -- R07_ALIQST        |     |    |    | NUM(15,2) NN       |
   -- R07_BCST          |     |    |    | NUM(15,2) NN       |
   -- R07_VLRST         |     |    |    | NUM(15,2) NN       |
   -- R07_TOTALITEM     |     |    |    | NUM(15,2) NN       |
   -- R07_ENTSAI        | CC  |    |    | VC(1) NN           | Somente para facilitar estoque   
   -- R07_ATIVO         | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- R07_REG           | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- R07_CODUSR        | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO       | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB        | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D26            | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31            | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ------------------------------------------------------------------------------------------------------------------
--
--    
/*    
-- antiga R08TCLIENTE 
CREATE TABLE R08FAVORECIDO(
  R08_CODFVR INTEGER NOT NULL,
  R08_CODEMP INTEGER NOT NULL,
  R08_CODBAN INTEGER NOT NULL,
  R08_CONTABANCO VARCHAR(30),
  R08_CODBANCO VARCHAR(5),
  R08_AGENCIA VARCHAR(5),
  R08_DVAGENCIA VARCHAR(1),
  R08_DVCONTACORRENTE VARCHAR(1),
  R08_DVAGENCIACONTA VARCHAR(1),
  R08_TIPO VARCHAR(1) NOT NULL,
  R08_AGRUPARBOLETO VARCHAR(1) NOT NULL,
  R08_CODDIR INTEGER NOT NULL,
  R08_CODUSR INTEGER NOT NULL,
  R08_SYS VARCHAR(1) NOT NULL,
CONSTRAINT R08TCLIENTE PRIMARY KEY (R08_CODFVR, R08_CODEMP));
*/
-------------------------------------------------------------------------------------
--                                R 1 0 N F S E R V I C O                          --
--tblr10
-------------------------------------------------------------------------------------
GO
CREATE TABLE R10NFSERVICO(
  R10_GUIA INTEGER NOT NULL
  ,R10_ITEM INTEGER NOT NULL
  ,R10_CODSRV INTEGER NOT NULL                --SERVICO
  ,R10_UNIDADES NUMERIC(15,4) NOT NULL
  ,R10_VLRUNITARIO NUMERIC(15,2) NOT NULL
  ,R10_VLRITEM NUMERIC(15,2) NOT NULL
  ,R10_VLRDESCONTO NUMERIC(15,2) NOT NULL
  ,R10_ALIQINSSFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_BCINSSFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_VLRINSSFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_ALIQINSS NUMERIC(15,2) NOT NULL
  ,R10_BCINSS NUMERIC(15,2) NOT NULL
  ,R10_VLRINSS NUMERIC(15,2) NOT NULL
  ,R10_ALIQIRRFFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_BCIRRFFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_VLRIRRFFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_ALIQIRRF NUMERIC(15,2) NOT NULL
  ,R10_BCIRRF NUMERIC(15,2) NOT NULL
  ,R10_VLRIRRF NUMERIC(15,2) NOT NULL
  ,R10_ALIQPISFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_BCPISFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_VLRPISFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_ALIQPIS NUMERIC(15,2) NOT NULL
  ,R10_BCPIS NUMERIC(15,2) NOT NULL
  ,R10_VLRPIS NUMERIC(15,2) NOT NULL
  ,R10_ALIQCOFINSFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_BCCOFINSFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_VLRCOFINSFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_ALIQCOFINS NUMERIC(15,2) NOT NULL
  ,R10_BCCOFINS NUMERIC(15,2) NOT NULL
  ,R10_VLRCOFINS NUMERIC(15,2) NOT NULL
  ,R10_ALIQCSLL NUMERIC(15,2) NOT NULL
  ,R10_BCCSLL NUMERIC(15,2) NOT NULL
  ,R10_VLRCSLL NUMERIC(15,2) NOT NULL
  ,R10_ALIQISSFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_BCISSFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_VLRISSFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_ALIQISS NUMERIC(15,2) NOT NULL
  ,R10_BCISS NUMERIC(15,2) NOT NULL
  ,R10_VLRISS NUMERIC(15,2) NOT NULL
  ,R10_ALIQCSLLFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_BCCSLLFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_VLRCSLLFAT NUMERIC(15,2) DEFAULT 0 NOT NULL
  ,R10_CSTPIS VARCHAR(3) NOT NULL             --CSTPIS
  ,R10_CSTCOFINS VARCHAR(3) NOT NULL          --CSTPIS
  ,R10_ENTSAI VARCHAR(1) NOT NULL
  ,R10_ATIVO VARCHAR(1) NOT NULL
  ,R10_REG VARCHAR(1) NOT NULL
  ,R10_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_R10EntSai CHECK( R10_ENTSAI IN('E','S'))  
  ,CONSTRAINT chk_R10Ativo CHECK( R10_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_R10Reg CHECK( R10_REG IN('A','P','S'))
  ,CONSTRAINT PKR10NFPRODUTO PRIMARY KEY (R10_GUIA, R10_ITEM));
GO
CREATE VIEW VR10NFSERVICO AS
  SELECT R10_GUIA
         ,R10_ITEM
         ,R10_CODSRV
         ,R10_UNIDADES
         ,R10_VLRUNITARIO
         ,R10_VLRITEM
         ,R10_VLRDESCONTO
         ,R10_ALIQINSSFAT
         ,R10_BCINSSFAT
         ,R10_VLRINSSFAT
         ,R10_ALIQINSS
         ,R10_BCINSS
         ,R10_VLRINSS
         ,R10_ALIQIRRFFAT
         ,R10_BCIRRFFAT
         ,R10_VLRIRRFFAT
         ,R10_ALIQIRRF
         ,R10_BCIRRF
         ,R10_VLRIRRF
         ,R10_ALIQPISFAT
         ,R10_BCPISFAT
         ,R10_VLRPISFAT
         ,R10_ALIQPIS
         ,R10_BCPIS
         ,R10_VLRPIS
         ,R10_ALIQCOFINSFAT
         ,R10_BCCOFINSFAT
         ,R10_VLRCOFINSFAT
         ,R10_ALIQCOFINS
         ,R10_BCCOFINS
         ,R10_VLRCOFINS
         ,R10_ALIQCSLL
         ,R10_BCCSLL
         ,R10_VLRCSLL
         ,R10_ALIQISSFAT
         ,R10_BCISSFAT
         ,R10_VLRISSFAT
         ,R10_ALIQISS
         ,R10_BCISS
         ,R10_VLRISS
         ,R10_ALIQCSLLFAT
         ,R10_BCCSLLFAT
         ,R10_VLRCSLLFAT
         ,R10_CSTPIS
         ,R10_CSTCOFINS
         ,R10_ENTSAI
         ,R10_ATIVO
         ,R10_REG
         ,R10_CODUSR
    FROM R10NFSERVICO
   -- ------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO             |INS  |UPD |DEL | TIPO               | Obs
   -- ------------------|-----|----|----|--------------------|----------------------------------------------------------
   -- R10_GUIA          | PK  |    |    | INT NN             |
   -- R10_ITEM          | PK  |    |    | INT NN             |
   -- R10_CODSRV        | SEL |    |    | INT NN             | Campo relacionado (SERVICO)   
   -- SRV_NOME          | SEL |    |    | VC(60) NN          | Campo relacionado (SERVICO)      
   -- R10_UNIDADES      |     |    |    | NUM(15,4) NN       |
   -- R10_VLRUNITARIO   |     |    |    | NUM(15,2) NN       |
   -- R10_VLRITEM       |     |    |    | NUM(15,2) NN       |
   -- R10_VLRDESCONTO   |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQINSSFAT   |     |    |    | NUM(15,2) NN       |
   -- R10_BCINSSFAT     |     |    |    | NUM(15,2) NN       |
   -- R10_VLRINSSFAT    |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQINSS      |     |    |    | NUM(15,2) NN       |
   -- R10_BCINSS        |     |    |    | NUM(15,2) NN       |
   -- R10_VLRINSS       |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQIRRFFAT   |     |    |    | NUM(15,2) NN       |
   -- R10_BCIRRFFAT     |     |    |    | NUM(15,2) NN       |
   -- R10_VLRIRRFFAT    |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQIRRF      |     |    |    | NUM(15,2) NN       |
   -- R10_BCIRRF        |     |    |    | NUM(15,2) NN       |
   -- R10_VLRIRRF       |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQPISFAT    |     |    |    | NUM(15,2) NN       |
   -- R10_BCPISFAT      |     |    |    | NUM(15,2) NN       |
   -- R10_VLRPISFAT     |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQPIS       |     |    |    | NUM(15,2) NN       |
   -- R10_BCPIS         |     |    |    | NUM(15,2) NN       |
   -- R10_VLRPIS        |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQCOFINSFAT |     |    |    | NUM(15,2) NN       |
   -- R10_BCCOFINSFAT   |     |    |    | NUM(15,2) NN       |
   -- R10_VLRCOFINSFAT  |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQCOFINS    |     |    |    | NUM(15,2) NN       |
   -- R10_BCCOFINS      |     |    |    | NUM(15,2) NN       |
   -- R10_VLRCOFINS     |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQCSLL      |     |    |    | NUM(15,2) NN       |
   -- R10_BCCSLL        |     |    |    | NUM(15,2) NN       |
   -- R10_VLRCSLL       |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQISSFAT    |     |    |    | NUM(15,2) NN       |
   -- R10_BCISSFAT      |     |    |    | NUM(15,2) NN       |
   -- R10_VLRISSFAT     |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQISS       |     |    |    | NUM(15,2) NN       |
   -- R10_BCISS         |     |    |    | NUM(15,2) NN       |
   -- R10_VLRISS        |     |    |    | NUM(15,2) NN       |
   -- R10_ALIQCSLLFAT   |     |    |    | NUM(15,2) NN       |
   -- R10_BCCSLLFAT     |     |    |    | NUM(15,2) NN       |
   -- R10_VLRCSLLFAT    |     |    |    | NUM(15,2) NN       |
   -- R10_CSTPIS        | SEL |    |    | VC(3) NN           | Campo relacionado (CSTPIS)
   -- PIS_NOME          | SEL |    |    | VC(60) NN          | Campo relacionado (CSTPIS)   
   -- R10_CSTCOFINS     | SEL |    |    | VC(3) NN           | Campo relacionado (CSTPIS)  
   -- R10_ENTSAI        | CC  |    |    | VC(1) NN           |    
   -- R10_ATIVO         | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- R10_REG           | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- R10_CODUSR        | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO       | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB        | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D05            | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31            | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ------------------------------------------------------------------------------------------------------------------      
--
--    

/*
CREATE TABLE R27TPRODUTO(
  R27_UFORIGEM VARCHAR(3) NOT NULL,
  R27_UFDESTINO VARCHAR(3) NOT NULL,
  R27_CODNCM VARCHAR(10) NOT NULL,
  R27_ALIQINTERNA NUMERIC(15,2) NOT NULL,
  R27_ALIQFP NUMERIC(15,2) NOT NULL,
  R27_ALIQBC NUMERIC(15,2) NOT NULL,
  R27_ALIQORIGEM NUMERIC(15,2) NOT NULL,
  R27_SYS VARCHAR(1) NOT NULL,
  R27_CODUSR INTEGER NOT NULL,
  R27_CODEMP INTEGER NOT NULL,
  R27_CODDIR INTEGER NOT NULL,
  R27_ATIVO VARCHAR(1) NOT NULL,
CONSTRAINT PKR27_TPRODUTO PRIMARY KEY (R27_UFORIGEM, R27_UFDESTINO, R27_NCM));
*/
-------------------------------------------------------------------------------------
--                                   R A T E I O                                   --
--tblrateio
-------------------------------------------------------------------------------------
CREATE TABLE RATEIO(
  RAT_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,RAT_CODCC VARCHAR(15) NOT NULL             --CONTACONTABIL
  ,RAT_GUIA INTEGER NOT NULL
  ,RAT_DEBITO NUMERIC(15,2)  DEFAULT 0 NOT NULL
  ,RAT_CREDITO NUMERIC(15,2)  DEFAULT 0 NOT NULL
  ,RAT_DATA DATE DEFAULT GETDATE() NOT NULL
  ,RAT_CODEMP INTEGER NOT NULL
  ,RAT_CODCMP INTEGER NOT NULL                --COMPETENCIA
  ,RAT_CONTABIL VARCHAR(1) NOT NULL
  ,RAT_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_RatContabil CHECK( RAT_CONTABIL IN('S','N'))
);  
GO
CREATE VIEW VRATEIO AS
  SELECT RAT_CODIGO
         ,RAT_CODCC
         ,RAT_GUIA
         ,RAT_DEBITO
         ,RAT_CREDITO
         ,RAT_DATA
         ,RAT_CODEMP
         ,RAT_CODCMP
         ,RAT_CONTABIL
         ,RAT_CODUSR
    FROM RATEIO
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- RAT_CODIGO     | PK  |    |    | INT                | Auto incremento
   -- RAT_CODCC      | SEL |    |    | VC(15) NN          | Campo relacionado (CONTAGERENCIAL)
   -- CC_NOME        | SEL |    |    | VC(40) NN          | Campo relacionado (CONTAGERENCIAL)   
   -- RAT_GUIA       |     |    |    | INT NN             |    
   -- RAT_DEBITO     |     |    |    | NUM(15,2) NN       |    
   -- RAT_CREDITO    |     |    |    | NUM(15,2) NN       |  
   -- RAT_DATA       | DEF |    |    | DAT NN             | Automatico pelo default
   -- RAT_CODCMP     | SEL |    |    | INT NN             | Campo relacionado (COMPETENCIA)
   -- CMP_NOME       | SEL |    |    | VC(6) NN           | Campo relacionado (COMPETENCIA)        
   -- RAT_CONTABIL   | CC  |    |    | VC(1) NN           | Se entra no contabil/balanco/razao   
   -- RAT_CODEMP     | SEL |    |    | INT NN             | Campo relacionado (EMPRESA) - Somente para facilitar os filtros 
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- RAT_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- RAT_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D28         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                                 R E G I A O                                     --
--tblregiao
-------------------------------------------------------------------------------------
GO
CREATE TABLE dbo.REGIAO(
  REG_CODIGO VARCHAR(5) PRIMARY KEY NOT NULL
  ,REG_NOME VARCHAR(20) NOT NULL
  ,REG_CODPAI INTEGER NOT NULL  
  ,REG_ATIVO VARCHAR(1) NOT NULL
  ,REG_REG VARCHAR(1) NOT NULL
  ,REG_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_RegAtivo CHECK( REG_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_RegReg CHECK( REG_REG IN('A','P','S'))
);
GO
CREATE VIEW VREGIAO AS
  SELECT REG_CODIGO,REG_NOME,REG_CODPAI,REG_ATIVO,REG_REG,REG_CODUSR FROM REGIAO
GO
INSERT INTO dbo.REGIAO( REG_CODIGO,REG_NOME,REG_CODPAI,REG_ATIVO ,REG_REG ,REG_CODUSR) VALUES('NO'  ,'NORTE'        ,1058,'S','P',1);   
INSERT INTO dbo.REGIAO( REG_CODIGO,REG_NOME,REG_CODPAI,REG_ATIVO ,REG_REG ,REG_CODUSR) VALUES('ND'  ,'NORDESTE'     ,1058,'S','P',1);   
INSERT INTO dbo.REGIAO( REG_CODIGO,REG_NOME,REG_CODPAI,REG_ATIVO ,REG_REG ,REG_CODUSR) VALUES('SUL' ,'SUL'          ,1058,'S','P',1);   
INSERT INTO dbo.REGIAO( REG_CODIGO,REG_NOME,REG_CODPAI,REG_ATIVO ,REG_REG ,REG_CODUSR) VALUES('SD'  ,'SUDESTE'      ,1058,'S','P',1);   
INSERT INTO dbo.REGIAO( REG_CODIGO,REG_NOME,REG_CODPAI,REG_ATIVO ,REG_REG ,REG_CODUSR) VALUES('CO'  ,'CENTRO OESTE' ,1058,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- REG_CODIGO     | PK  |    |    | VC(05) NN          |
   -- REG_NOME       |     |    |    | VC(20) NN          |
   -- REG_CODPAI     | SEL |    |    | INT NN             | Campo relacionado (PAIS)
   -- PAI_NOME       | SEL |    |    | VC(30) NN          | Campo relacionado (PAIS)     
   -- REG_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- REG_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- REG_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D08         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                 R E G R A                                       --
--Duvidaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
-------------------------------------------------------------------------------------
/*
CREATE TABLE REGRA(
  RGR_CODIGO VARCHAR(15) NOT NULL,
  RGR_CODEMP INTEGER NOT NULL,  
  RGR_STRING VARCHAR(60),
  RGR_DESCRICAO VARCHAR(60),
  RGR_SYS VARCHAR(1) NOT NULL,
  RGR_ATIVO VARCHAR(1) NOT NULL,  
CONSTRAINT PKTREGRA PRIMARY KEY (REG_CODIGO, REG_CODEMP)
);
*/
-------------------------------------------------------------------------------------
--                                 S E R I E N F                                   --
-- Tabela multi-empresa                                                            --
--tblserienf
-------------------------------------------------------------------------------------
GO
CREATE TABLE SERIENF(
  SNF_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,SNF_SERIE VARCHAR(4) NOT NULL
  ,SNF_ENTSAI VARCHAR(1) NOT NULL
  ,SNF_CODTD VARCHAR(3) NOT NULL
  ,SNF_CODEMP INTEGER NOT NULL
  ,SNF_INFORMARNF VARCHAR(1) NOT NULL
  ,SNF_NFINICIO INTEGER NOT NULL
  ,SNF_NFFIM INTEGER NOT NULL
  ,SNF_IDF VARCHAR(20) NOT NULL
  ,SNF_MODELO VARCHAR(5) NOT NULL -- Faltou tabela relacionada
  ,SNF_CODEMP INTEGER NOT NULL  
  ,SNF_ATIVO VARCHAR(1) NOT NULL
  ,SNF_REG VARCHAR(1) NOT NULL
  ,SNF_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_SnfEntSai CHECK( SNF_ENTSAI IN('E','F'))    
  ,CONSTRAINT chk_SnfInformarNf CHECK( SNF_INFORMARNF IN('S','N'))      
  ,CONSTRAINT chk_SnfAtivo CHECK( SNF_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_SnfReg CHECK( SNF_REG IN('A','P','S'))
);
GO
CREATE VIEW VSERIENF AS
  SELECT SNF_CODIGO,SNF_SERIE,SNF_ENTSAI,SNF_CODTD,SNF_CODEMP,SNF_INFORMARNF,SNF_NFINICIO,SNF_NFFIM
         ,SNF_IDF,SNF_MODELO,SNF_CODEMP,SNF_ATIVO,SNF_REG,SNF_CODUSR FROM SERIENF
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- SNF_CODIGO     | PK  |    |    | INT                | Auto incremento
   -- SNF_SERIE      |     |    |    | VC(4) NN           | 
   -- SNF_ENTSAI     | CC  |    |    | VC(1) NN           |    
   -- SNF_CODTD      | SEL |    |    | VC(3) NN           | Campo relacionado (TIPODOCUMENTO)   
   -- TD_NOME        | SEL |    |    | VC(20) NN          | Campo relacionado (TIPODOCUMENTO)      
   -- SNF_CODEMP     | SEL |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- SNF_INFORMARNF | CC  |    |    | VC(1) NN           |    
   -- SNF_NFINICIO   |     |    |    | INT NN             |
   -- SNF_NFFIM      |     |    |    | INT NN             |   
   -- SNF_IDF        |     |    |    | VC(20) NN          |   
   -- SNF_MODELO     |     |    |    | VC(5) NN           | 
   -- SNF_CODEMP     | SEL |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- SNF_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- SNF_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- SNF_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D22         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
-------------------------------------------------------------------------------------
--                                 S E R V I C O                                   --
-- Tabela multi-empresa                                                            --
--tblservico
-------------------------------------------------------------------------------------
GO
CREATE TABLE SERVICO(
  SRV_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL 
  ,SRV_NOME VARCHAR(60) NOT NULL
  ,SRV_ENTSAI VARCHAR(1) NOT NULL
  ,SRV_INSS VARCHAR(1) NOT NULL
  ,SRV_INSSALIQ NUMERIC(6,2) NOT NULL
  ,SRV_INSSBASECALC NUMERIC(6,2) NOT NULL
  ,SRV_IRRF VARCHAR(1) NOT NULL
  ,SRV_IRRFALIQ NUMERIC(6,2) NOT NULL
  ,SRV_PIS VARCHAR(1) NOT NULL
  ,SRV_PISALIQ NUMERIC(6,2) NOT NULL
  ,SRV_COFINS VARCHAR(1) NOT NULL
  ,SRV_COFINSALIQ NUMERIC(6,2) NOT NULL
  ,SRV_CSLL VARCHAR(1) NOT NULL
  ,SRV_CSLLALIQ NUMERIC(6,2) NOT NULL
  ,SRV_ISS VARCHAR(1) NOT NULL
  ,SRV_CODCC VARCHAR(15) NOT NULL           --CONTACONTABIL
  ,SRV_CODSPR VARCHAR(10) NOT NULL          --SERVICOPREFEITURA
  ,SRV_CODPRD VARCHAR(15)
  ,SRV_CODEMP INTEGER NOT NULL
  ,SRV_ATIVO VARCHAR(1) NOT NULL
  ,SRV_REG VARCHAR(1) NOT NULL
  ,SRV_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_SrvEntSai CHECK( SRV_ENTSAI IN('E','S'))  
  ,CONSTRAINT chk_SrvInss CHECK( SRV_INSS IN('S','N'))  
  ,CONSTRAINT chk_SrvIrrf CHECK( SRV_IRRF IN('S','N'))    
  ,CONSTRAINT chk_SrvPis CHECK( SRV_PIS IN('S','N'))      
  ,CONSTRAINT chk_SrvCofins CHECK( SRV_COFINS IN('S','N'))      
  ,CONSTRAINT chk_SrvCsll CHECK( SRV_CSLL IN('S','N'))      
  ,CONSTRAINT chk_SrvIss CHECK( SRV_ISS IN('S','N'))      
  ,CONSTRAINT chk_SrvAtivo CHECK( SRV_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_SrvReg CHECK( SRV_REG IN('A','P','S')));
GO
CREATE VIEW VSERVICO AS
  SELECT SRV_CODIGO
         ,SRV_NOME
         ,SRV_ENTSAI
         ,SRV_INSS
         ,SRV_INSSALIQ
         ,SRV_INSSBASECALC
         ,SRV_IRRF
         ,SRV_IRRFALIQ
         ,SRV_PIS
         ,SRV_PISALIQ
         ,SRV_COFINS
         ,SRV_COFINSALIQ
         ,SRV_CSLL
         ,SRV_CSLLALIQ
         ,SRV_ISS
         ,SRV_CODCC
         ,SRV_CODSPR
         ,SRV_CODPRD
         ,SRV_CODEMP
         ,SRV_ATIVO
         ,SRV_REG
         ,SRV_CODUSR
    FROM SERVICO
   -- -----------------|-----|----|----|------------------|----------------------------------------------------------   
   -- CAMPO            |INS  |UPD |DEL | TIPO             | Obs
   -- -----------------|-----|----|----|------------------|----------------------------------------------------------
   -- SRV_CODIGO       | PK  |    |    | INT              |  Auto incremento
   -- SRV_NOME         |     |    |    | VC(60) NN        |
   -- SRV_ENTSAI       | CC  |    |    | VC(1) NN         |    
   -- SRV_INSS         | CC  |    |    | VC(1) NN         |      
   -- SRV_INSSALIQ     |     |    |    | NUM(6,2) NN      |   
   -- SRV_INSSBASECALC |     |    |    | NUM(6,2) NN      |   
   -- SRV_IRRF         | CC  |    |    | VC(1) NN         |      
   -- SRV_IRRFALIQ     |     |    |    | NUM(6,2) NN      |   
   -- SRV_PIS          | CC  |    |    | VC(1) NN         |      
   -- SRV_PISALIQ      |     |    |    | NUM(6,2) NN      |      
   -- SRV_COFINS       | CC  |    |    | VC(1) NN         |      
   -- SRV_COFINSALIQ   |     |    |    | NUM(6,2) NN      |      
   -- SRV_CSLL         | CC  |    |    | VC(1) NN         |      
   -- SRV_CSLLALIQ     |     |    |    | NUM(6,2) NN      |      
   -- SRV_ISS          | CC  |    |    | VC(1) NN         |      
   -- SRV_CODCC        | SEL |    |    | VC(15) NN        | Campo relacionado (CONTAGERENCIAL)
   -- CC_NOME          | SEL |    |    | VC(40) NN        | Campo relacionado (CONTAGERENCIAL)   
   -- SRV_CODSPR       | SEL |    |    | VC(10) NN        | Campo relacionado (SERVICOPREFEITURA)
   -- SPR_NOME         | SEL |    |    | VC(60) NN        | Campo relacionado (SERVICOPREFEITURA)   
   -- SRV_CODPRD       |     |    |    | VC(15)           | Codigo do produto se existir
   -- SRV_CODEMP       | SEL |    |    | INT NN           | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO      | SEL |    |    | VC(15) NN        | Campo relacionado (EMPRESA)     
   -- SRV_ATIVO        | CC  |    |    | VC(1) NN         | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- SRV_REG          | FNC |    |    | VC(1) NN         | P|A|S   P=Publico  A=Administrador S=Sistema
   -- SRV_CODUSR       | OK  |    |    | INT NN           | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO      | SEL |    |    | VC(15) NN        | Campo relacionado (USUARIO)
   -- USR_ADMPUB       | SEL |    |    | VC(1) NN         | Retornar se o usuario eh PUB/ADM
   -- UP_D04           | SEL |    |    | INT NN           | Recupera o direito de usuario para esta tabela
   -- UP_D31           | SEL |    |    | INT NN           | Recupera o direito se pode transformar registro do sistema
   -- -----------------|-----|----|----|------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                       S E R V I C O P R E F E I T U R A                         --
--tblservicoprefeitura
-------------------------------------------------------------------------------------
GO
CREATE TABLE SERVICOPREFEITURA(
  SPR_CODIGO VARCHAR(10) NOT NULL
  ,SPR_CODCDD VARCHAR(7) NOT NULL
  ,SPR_NOME VARCHAR(60) NOT NULL
  ,SPR_CODFEDERAL VARCHAR(10) NOT NULL
  ,SPR_ALIQUOTA NUMERIC(6,2) NOT NULL
  ,SPR_RETIDO VARCHAR(1) NOT NULL
  ,SPR_CODEMP INTEGER NOT NULL
  ,SPR_ATIVO VARCHAR(1) NOT NULL
  ,SPR_REG VARCHAR(1) NOT NULL
  ,SPR_CODUSR INTEGER NOT NULL
  ,CONSTRAINT chk_SprRetido CHECK( SPR_RETIDO IN('S','N'))  
  ,CONSTRAINT chk_SprAtivo CHECK( SPR_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_SprReg CHECK( SPR_REG IN('A','P','S'))
  ,CONSTRAINT PKTSERVICOPREFEITURA PRIMARY KEY (SPR_CODIGO, SPR_CODCDD));
GO
CREATE VIEW VSERVICOPREFEITURA AS
  SELECT SPR_CODIGO
         ,SPR_CODCDD
         ,SPR_NOME
         ,SPR_CODFEDERAL
         ,SPR_ALIQUOTA
         ,SPR_RETIDO
         ,SPR_CODEMP
         ,SPR_ATIVO
         ,SPR_REG
         ,SPR_CODUSR
  FROM SERVICOPREFEITURA
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- SPR_CODIGO     | PK  |    |    | VC(10) NN          | Campo informado 
   -- SPR_CODCDD     | SEL |    |    | VC(7) NN           | Campo relacionado (CIDADE)   
   -- CDD_NOME       | SEL |    |    | VC(30) NN          | Campo relacionado (CIDADE)      
   -- SPR_NOME       |     |    |    | VC(60) NN          |
   -- SPR_CODFEDERAL |     |    |    | VC(10) NN          |
   -- SPR_ALIQUOTA   |     |    |    | NUM(6,2) NN        |
   -- SPR_RETIDO     | CC  |    |    | VC(1) NN           |      
   -- SPR_CODEMP     | SEL |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- SPR_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- SPR_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- SPR_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D04         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                                  S P E D                                        --
-- Classificacao da natureza do sped                                               --
--tblsped
-------------------------------------------------------------------------------------
GO
CREATE TABLE SPED(
  SPD_CODIGO VARCHAR(2) PRIMARY KEY NOT NULL
  ,SPD_NOME VARCHAR(20) NOT NULL
  ,SPD_ATIVO VARCHAR(1) NOT NULL
  ,SPD_REG VARCHAR(1) NOT NULL
  ,SPD_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_SpdAtivo CHECK( SPD_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_SpdReg CHECK( SPD_REG IN('A','P','S')));
GO
CREATE VIEW VSPED AS
  SELECT SPD_CODIGO,SPD_NOME,SPD_ATIVO,SPD_REG,SPD_CODUSR FROM SPED
GO  
INSERT INTO dbo.VSPED(SPD_CODIGO,SPD_NOME,SPD_ATIVO ,SPD_REG ,SPD_CODUSR) VALUES('**','NAO ENTRA SPED'        ,'S'  ,'S'  ,1);
INSERT INTO dbo.VSPED(SPD_CODIGO,SPD_NOME,SPD_ATIVO ,SPD_REG ,SPD_CODUSR) VALUES('00','CONTA SINTETICA'       ,'S'  ,'S'  ,1);
INSERT INTO dbo.VSPED(SPD_CODIGO,SPD_NOME,SPD_ATIVO ,SPD_REG ,SPD_CODUSR) VALUES('01','CONTAS DE ATIVO'       ,'S'  ,'S'  ,1);
INSERT INTO dbo.VSPED(SPD_CODIGO,SPD_NOME,SPD_ATIVO ,SPD_REG ,SPD_CODUSR) VALUES('02','CONTAS DE PASSIVO'     ,'S'  ,'S'  ,1);
INSERT INTO dbo.VSPED(SPD_CODIGO,SPD_NOME,SPD_ATIVO ,SPD_REG ,SPD_CODUSR) VALUES('03','PATRIMONIO LIQUIDO'    ,'S'  ,'S'  ,1);
INSERT INTO dbo.VSPED(SPD_CODIGO,SPD_NOME,SPD_ATIVO ,SPD_REG ,SPD_CODUSR) VALUES('04','CONTA DE RESULTADO'    ,'S'  ,'S'  ,1);
INSERT INTO dbo.VSPED(SPD_CODIGO,SPD_NOME,SPD_ATIVO ,SPD_REG ,SPD_CODUSR) VALUES('05','CONTA DE COMPENSACAO'  ,'S'  ,'S'  ,1);
INSERT INTO dbo.VSPED(SPD_CODIGO,SPD_NOME,SPD_ATIVO ,SPD_REG ,SPD_CODUSR) VALUES('09','OUTRAS'                ,'S'  ,'S'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- SPD_CODIGO     | PK  |    |    | VC(02) NN          |
   -- SPD_NOME       |     |    |    | VC(20) NN          |
   -- SPD_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- SPD_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- SPD_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D12         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                         T I P O D O C U M E N T O                               --
-- Este informa em SERIENF qual serie buscar                                       --
--tbltipodocumento
-------------------------------------------------------------------------------------
CREATE TABLE TIPODOCUMENTO(
  TD_CODIGO VARCHAR(3) PRIMARY KEY NOT NULL
  ,TD_NOME VARCHAR(20) NOT NULL
  ,TD_ATIVO VARCHAR(1) NOT NULL
  ,TD_REG VARCHAR(1) NOT NULL
  ,TD_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_TdAtivo CHECK( TD_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_TdReg CHECK( TD_REG IN('A','P','S')));
GO
CREATE VIEW VTIPODOCUMENTO AS
  SELECT TD_CODIGO,TD_NOME,TD_ATIVO,TD_REG,TD_CODUSR FROM TIPODOCUMENTO
GO  
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('CON','CONTRATO'            ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('CX','CAIXINHA'             ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('DEP','DEPOSITO'            ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('DN','DARF NORMAL'          ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('EF','EXTRA FINANCEIRO'     ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('FAT','FATURA'              ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('GNR','GNRE'                ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('GR','GUIA RECOLHIMENTO'    ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('ND','NOTA DEBITO'          ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('NFP','NF PRODUTO'          ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('NFS','NF SERVICO'          ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('REC','RECIBO'              ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('REQ','REQUISICAO'          ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('RPA','REC PAGTO AUTONOMO'  ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('RPS','REC PAGTO SERVICO'   ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('TAR','TARIFA BANCARIA'     ,'S' ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('TEC','TRANSF ENTRE CONTAS' ,'S'  ,'S'  ,1);
INSERT INTO dbo.VTIPODOCUMENTO(TD_CODIGO,TD_NOME,TD_ATIVO ,TD_REG ,TD_CODUSR) VALUES('TED','TED'                 ,'S'  ,'S'  ,1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- TD_CODIGO      | PK  |    |    | VC(03) NN          |
   -- TD_NOME        |     |    |    | VC(20) NN          |
   -- TD_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- TD_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- TD_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D20         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             T R A N S P O R T A D O R A                         --
--tbltransportadora
-------------------------------------------------------------------------------------
GO
CREATE TABLE TRANSPORTADORA(
  TRN_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,TRN_NOME VARCHAR(60) NOT NULL  
  ,TRN_APELIDO VARCHAR(15) NOT NULL  
  ,TRN_CEP VARCHAR(8) NOT NULL  
  ,TRN_CODCDD VARCHAR(7) NOT NULL       --CIDADE
  ,TRN_CNPJ VARCHAR(14) NOT NULL
  ,TRN_CODLGR VARCHAR(5) NOT NULL       --LOGRADOURO
  ,TRN_ENDERECO VARCHAR(60) NOT NULL
  ,TRN_NUMERO VARCHAR(10) NOT NULL
  ,TRN_BAIRRO VARCHAR(15) NOT NULL  
  ,TRN_FONE VARCHAR(10)
  ,TRN_EMAIL VARCHAR(60)
  ,TRN_ATIVO VARCHAR(1) NOT NULL
  ,TRN_REG VARCHAR(1) NOT NULL
  ,TRN_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_TrnCnpj CHECK( TRN_CNPJ NOT LIKE '%[^0-9]%' )          
  ,CONSTRAINT chk_TrnCep CHECK( TRN_CEP LIKE('[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]'))    
  ,CONSTRAINT chk_TrnAtivo CHECK( TRN_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_TrnReg CHECK( TRN_REG IN('A','P','S')));
GO
CREATE VIEW VTRANSPORTADORA AS
  SELECT TRN_CODIGO
         ,TRN_NOME
         ,TRN_APELIDO
         ,TRN_CEP
         ,TRN_CODCDD
         ,TRN_CNPJ
         ,TRN_CODLGR
         ,TRN_ENDERECO
         ,TRN_NUMERO
         ,TRN_BAIRRO
         ,TRN_FONE
         ,TRN_EMAIL
         ,TRN_ATIVO
         ,TRN_REG
         ,TRN_CODUSR
  FROM TRANSPORTADORA
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- TRN_CODIGO     | PK  |    |    | INT                |  Auto incremento
   -- TRN_NOME       |     |    |    | VC(60) NN          |
   -- TRN_APELIDO    |     |    |    | VC(15) NN          |   
   -- TRN_CODCDD     | SEL |    |    | VC(7) NN           | Campo relacionado (CIDADE)   
   -- CDD_NOME       | SEL |    |    | VC(30) NN          | Campo relacionado (CIDADE)      
   -- TRN_CNPJ       |     |    |    | VC(14) NN          |      
   -- TRN_CODLGR     | SEL |    |    | VC(5) NN           | Campo relacionado (LOGRADOURO)   
   -- LGR_NOME       | SEL |    |    | VC(20) NN          | Campo relacionado (LOGRADOURO)      
   -- TRN_ENDERECO   |     |    |    | VC(60) NN          |
   -- TRN_NUMERO     |     |    |    | VC(10) NN          |   
   -- TRN_CEP        |     |    |    | VC(8) NN           |
   -- TRN_BAIRRO     |     |    |    | VC(15) NN          |   
   -- TRN_FONE       |     |    |    | VC(10) NN          |      
   -- TRN_EMAIL      |     |    |    | VC(60) NN          |         
   -- TRN_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- TRN_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- TRN_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D21         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--  
----------------------------------------------------------------------------------------------------------------------
--                                                 U S U A R I O                                                    --
--                                 A tabela USUARIO nasce com um registro ADMIN                                     --
--   Todo registro cadastrado nesta vai para a tabela USUARIOSISTEMA pois em USUARIO o registro pode ser excluido   --
-- Em USUARIOSISTEMA nenhum registro pode ser excluido ou alterado, esta serve de relacionamento para passo-a-passo --
--                              A tabela BKPUSUARIO guarda todas alteracoes feitas em USUARIO                       --
--tblusuario
----------------------------------------------------------------------------------------------------------------------
GO
CREATE TABLE dbo.USUARIO(
  USR_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,USR_CPF VARCHAR(11) NOT NULL
  ,USR_APELIDO VARCHAR(15) NOT NULL
  ,USR_CODUP INTEGER NOT NULL  
  ,USR_CODCRG VARCHAR(3) NOT NULL
  ,USR_EMAIL VARCHAR(60) NOT NULL
  ,USR_SENHA VARCHAR(15) NOT NULL
  ,USR_INTERNO VARCHAR(1) NOT NULL
  ,USR_ADMPUB VARCHAR(1) NOT NULL
  ,USR_VENCTO DATE DEFAULT GETDATE() NOT NULL
  ,USR_PRIMEIROACESSO VARCHAR(1) DEFAULT 'S' NOT NULL
  ,USR_ATIVO VARCHAR(1) NOT NULL
  ,USR_REG VARCHAR(1) NOT NULL
  ,USR_CODUSR  INTEGER NOT NULL
  ,CONSTRAINT chk_UsrCpf CHECK( USR_CPF NOT LIKE '%[^0-9]%' )  
  ,CONSTRAINT chk_UsrInterno CHECK( USR_INTERNO IN('I','E','D'))
  ,CONSTRAINT chk_UsrAtivo CHECK( USR_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_UsrReg CHECK( USR_REG IN('A','P','S'))
  ,CONSTRAINT chk_UsrAdmPub CHECK( USR_ADMPUB IN('A','P'))
  ,CONSTRAINT chk_UsrPrimeiroAcesso CHECK( USR_PRIMEIROACESSO IN('S','N'))
);
GO
CREATE VIEW VUSUARIO AS
  SELECT USR_CODIGO,USR_CPF,USR_APELIDO,USR_CODUP,USR_CODCRG,USR_EMAIL,USR_SENHA,USR_INTERNO,USR_ATIVO,USR_REG
         ,USR_ADMPUB,USR_VENCTO,USR_PRIMEIROACESSO,USR_CODUSR
    FROM USUARIO;
   -- -------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO              |INS  |UPD |DEL | TIPO               | Obs
   -- -------------------|-----|----|----|--------------------|----------------------------------------------------------
   -- USR_CODIGO         |     |    |    | INT NN             | Auto incremento
   -- USR_CPF            |     |    |    | VC(11) NN          |
   -- USR_APELIDO        |     |    |    | VC(15) NN          |
   -- USR_CODUP          | SEL |    |    | NT NN              | Campo relacionado (USUARIOPERFIL) 
   -- UP_NOME            | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIOPERFIL)      
   -- USR_CODCRG         |     |    |    | VC(3) NN           |
   -- CRG_NOME           | SEL |    |    | VC(20) NN          | Campo relacionado (CARGO)   
   -- USR_EMAIL          |     |    |    | VC(60) NN          |
   -- USR_SENHA          |     |    |    | VC(15) NN          |
   -- USR_INTERNO        | CC  |    |    | VC(1) NN           |
   -- USR_ATIVO          | CC  |    |    | VC(1) NN           |
   -- USR_REG            | CC  |    |    | VC(1) NN           |
   -- USR_ADMPUB         | CC  |    |    | VC(1) NN           |
   -- USR_VENCTO         |     |    |    | DATE               |
   -- USR_PRIMEIROACESSO | CC  |    |    | VC(1) NN           | Apenas para mensagem
   --                                                         | Se "S" Primeiro acesso necessario alterar sua senha  
   --                                                         | Se "N" Senha expirada, favor alterar
   -- USR_CODUSR         |     |    |    | INT NN             |
   -- USR_ADMPUB         | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D01             | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- -------------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- -------------------------------------------------------------------------------------------------------------------
   --
   --
-------------------------------------------------------------------------------------
--                             U S U A R I O E M P R E S A                         --
--                          Relacionamento entre usuario/empresa                   --
--tblusuarioempresa
-------------------------------------------------------------------------------------
GO
CREATE TABLE dbo.USUARIOEMPRESA(
  UE_CODUSR INTEGER NOT NULL
  ,UE_CODEMP INTEGER NOT NULL
  ,UE_ATIVO VARCHAR(1) NOT NULL
  ,UE_REG VARCHAR(1) NOT NULL 
  ,SIS_CODUSR  INTEGER NOT NULL  
  ,CONSTRAINT chk_UeAtivo CHECK( UE_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_UeReg CHECK( UE_REG IN('A','P','S'))
,CONSTRAINT PKUSUARIOEMPRESA PRIMARY KEY (UE_CODUSR,UE_CODEMP));
GO
CREATE VIEW VUSUARIOEMPRESA AS
  SELECT UE_CODUSR
         ,UE_CODEMP
         ,UE_ATIVO
         ,UE_REG
         ,SIS_CODUSR
    FROM USUARIOEMPRESA
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- UE_CODUSR      | PK  |    |    | INT NN             | Campo automatico     
   -- UE_CODEMP      | PK  |    |    | INT NN             | Campo relacionado (EMPRESA)  
   -- EMP_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (EMPRESA)     
   -- UE_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- UE_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- SIS_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D02         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--    
-------------------------------------------------------------------------------------
--                             U S U A R I O P E R F I L                           --
--                       A tabela nasce com um perfil de ADMIN                     --
--tblusuarioperfil
-------------------------------------------------------------------------------------
GO
CREATE TABLE dbo.USUARIOPERFIL(
  UP_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,UP_NOME VARCHAR(15) NOT NULL
  ,UP_D01 INTEGER NOT NULL
  ,UP_D02 INTEGER NOT NULL
  ,UP_D03 INTEGER NOT NULL
  ,UP_D04 INTEGER NOT NULL
  ,UP_D05 INTEGER NOT NULL
  ,UP_D06 INTEGER NOT NULL
  ,UP_D07 INTEGER NOT NULL
  ,UP_D08 INTEGER NOT NULL
  ,UP_D09 INTEGER NOT NULL
  ,UP_D10 INTEGER NOT NULL
  ,UP_D11 INTEGER NOT NULL
  ,UP_D12 INTEGER NOT NULL
  ,UP_D13 INTEGER NOT NULL
  ,UP_D14 INTEGER NOT NULL
  ,UP_D15 INTEGER NOT NULL
  ,UP_D16 INTEGER NOT NULL
  ,UP_D17 INTEGER NOT NULL
  ,UP_D18 INTEGER NOT NULL
  ,UP_D19 INTEGER NOT NULL
  ,UP_D20 INTEGER NOT NULL
  ,UP_D21 INTEGER NOT NULL
  ,UP_D22 INTEGER NOT NULL
  ,UP_D23 INTEGER NOT NULL
  ,UP_D24 INTEGER NOT NULL
  ,UP_D25 INTEGER NOT NULL
  ,UP_D26 INTEGER NOT NULL
  ,UP_D27 INTEGER NOT NULL
  ,UP_D28 INTEGER NOT NULL
  ,UP_D29 INTEGER NOT NULL
  ,UP_D30 INTEGER NOT NULL
  ,UP_D31 INTEGER NOT NULL
  ,UP_D32 INTEGER NOT NULL
  ,UP_D33 INTEGER NOT NULL
  ,UP_D34 INTEGER NOT NULL
  ,UP_D35 INTEGER NOT NULL
  ,UP_D36 INTEGER NOT NULL
  ,UP_D37 INTEGER NOT NULL
  ,UP_D38 INTEGER NOT NULL
  ,UP_D39 INTEGER NOT NULL
  ,UP_D40 INTEGER NOT NULL
  ,UP_ATIVO VARCHAR(1) NOT NULL
  ,UP_REG VARCHAR(1) NOT NULL
  ,UP_CODUSR  INTEGER NOT NULL  
  ,CONSTRAINT chk_UpAtivo CHECK( UP_ATIVO IN('S','N'))
  ,CONSTRAINT chk_UpReg CHECK( UP_REG IN('A','P','S'))
  ,CONSTRAINT chk_UpD01 CHECK( UP_D01 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD02 CHECK( UP_D02 BETWEEN 0 AND 4 )  
  ,CONSTRAINT chk_UpD03 CHECK( UP_D03 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD04 CHECK( UP_D04 BETWEEN 0 AND 4 )  
  ,CONSTRAINT chk_UpD05 CHECK( UP_D05 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD06 CHECK( UP_D06 BETWEEN 0 AND 4 )  
  ,CONSTRAINT chk_UpD07 CHECK( UP_D07 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD08 CHECK( UP_D08 BETWEEN 0 AND 4 )  
  ,CONSTRAINT chk_UpD09 CHECK( UP_D09 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD10 CHECK( UP_D10 BETWEEN 0 AND 4 )  
  ,CONSTRAINT chk_UpD11 CHECK( UP_D11 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD12 CHECK( UP_D12 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD13 CHECK( UP_D13 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD14 CHECK( UP_D14 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD15 CHECK( UP_D15 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD16 CHECK( UP_D16 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD17 CHECK( UP_D17 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD18 CHECK( UP_D18 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD19 CHECK( UP_D19 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD20 CHECK( UP_D20 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD21 CHECK( UP_D21 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD22 CHECK( UP_D22 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD23 CHECK( UP_D23 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD24 CHECK( UP_D24 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD25 CHECK( UP_D25 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD26 CHECK( UP_D26 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD27 CHECK( UP_D27 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD28 CHECK( UP_D28 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD29 CHECK( UP_D29 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD30 CHECK( UP_D30 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD31 CHECK( UP_D31 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD32 CHECK( UP_D32 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD33 CHECK( UP_D33 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD34 CHECK( UP_D34 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD35 CHECK( UP_D35 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD36 CHECK( UP_D36 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD37 CHECK( UP_D37 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD38 CHECK( UP_D38 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD39 CHECK( UP_D39 BETWEEN 0 AND 4 )
  ,CONSTRAINT chk_UpD40 CHECK( UP_D40 BETWEEN 0 AND 4 )
);
GO
CREATE VIEW VUSUARIOPERFIL AS
  SELECT UP_CODIGO,UP_NOME
         ,UP_D01,UP_D02,UP_D03,UP_D04,UP_D05,UP_D06,UP_D07,UP_D08,UP_D09,UP_D10
         ,UP_D11,UP_D12,UP_D13,UP_D14,UP_D15,UP_D16,UP_D17,UP_D18,UP_D19,UP_D20
         ,UP_D21,UP_D22,UP_D23,UP_D24,UP_D25,UP_D26,UP_D27,UP_D28,UP_D29,UP_D30
         ,UP_D31,UP_D32,UP_D33,UP_D34,UP_D35,UP_D36,UP_D37,UP_D38,UP_D39,UP_D40
         ,UP_ATIVO,UP_REG,UP_CODUSR FROM USUARIOPERFIL
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- UP_CODIGO      | PK  |    |    | INT                |  Auto incremento
   -- UP_NOME        |     |    |    | VC(15) NN          |
   -- UP_D01
   -- ATÉ
   -- UP_D40         | CC  |    |    | INT NN             |* 0|1|2|3|4
   -- UP_ATIVO       | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- UP_REG         | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- UP_CODUSR      | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D01         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
-------------------------------------------------------------------------------------
--                             U S U A R I O S I S T E M A                         --
-- A tabela USUARIOSISTEMA guarda todos usuarios ja cadastrados seja os ativos ou  --
--      excluidos, ela eh a referência para todo passo-a-passo do sistema          -- 
--tblusuariosistema
-------------------------------------------------------------------------------------
GO
CREATE TABLE dbo.USUARIOSISTEMA(
  US_CODIGO INTEGER PRIMARY KEY NOT NULL
  ,US_CPF VARCHAR(11) NOT NULL
  ,US_APELIDO VARCHAR(15) NOT NULL
  ,US_DTINCLUSAO  DATE DEFAULT GETDATE() NOT NULL
  ,US_DTEXCLUSAO DATE);
-------------------------------------------------------------------------------------
--                                 V E N D E D O R                                 --
--tblvendedor
-------------------------------------------------------------------------------------
GO  
CREATE TABLE VENDEDOR(
  VND_CODIGO INTEGER IDENTITY PRIMARY KEY NOT NULL
  ,VND_NOME VARCHAR(40) NOT NULL
  ,VND_ATIVO VARCHAR(1) NOT NULL
  ,VND_REG VARCHAR(1) NOT NULL
  ,VND_CODUSR INTEGER NOT NULL  
  ,CONSTRAINT chk_VndAtivo CHECK( VND_ATIVO IN('S','N'))  
  ,CONSTRAINT chk_VndReg CHECK( VND_REG IN('A','P','S')));
GO
CREATE VIEW VVENDEDOR AS
  SELECT VND_CODIGO,VND_NOME,VND_ATIVO,VND_REG,VND_CODUSR FROM VENDEDOR
GO
INSERT INTO VVENDEDOR VALUES(1  ,'AMAURI ALVES MARTINS'             ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(2  ,'ARTHUR AUGUSTO DE ANDRADE GOMES'  ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(3  ,'BRUNO WILLIAM QUEIROZ FERNANDES'  ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(4  ,'CAIO MARQUES RODRIGUES'           ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(5  ,'CLAUDIO LINS  TEIXEIRA'           ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(6  ,'DANIEL PERRI BREIA'               ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(7  ,'DANILO DE OLIVEIRA SILVA'         ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(8  ,'DIOGO GRADOFF CORTONESI'          ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(9  ,'DIOGO ROBERTO SARTORI'            ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(10 ,'DONALD JORGE THOMAZ'              ,'S','P',1);
INSERT INTO VVENDEDOR VALUES(11 ,'EDER RODRIGUES DOS SANTOS'        ,'S','P',1);
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- CAMPO          |INS  |UPD |DEL | TIPO               | Obs
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------
   -- VND_CODIGO     | PK  |    |    | INT                |  Auto incremento
   -- VND_NOME       |     |    |    | VC(40) NN          |
   -- VND_ATIVO      | CC  |    |    | VC(1) NN           | S|N     Se o registro pode ser usado em tabelas auxiliares
   -- VND_REG        | FNC |    |    | VC(1) NN           | P|A|S   P=Publico  A=Administrador S=Sistema
   -- VND_CODUSR     | OK  |    |    | INT NN             | Codigo do Usuario em USUARIO que esta tentando INC/ALT/EXC
   -- USR_APELIDO    | SEL |    |    | VC(15) NN          | Campo relacionado (USUARIO)
   -- USR_ADMPUB     | SEL |    |    | VC(1) NN           | Retornar se o usuario eh PUB/ADM
   -- UP_D29         | SEL |    |    | INT NN             | Recupera o direito de usuario para esta tabela
   -- UP_D31         | SEL |    |    | INT NN             | Recupera o direito se pode transformar registro do sistema
   -- ---------------|-----|----|----|--------------------|----------------------------------------------------------   
   -- [OK]=Checado no trigger   [CC]=Check constraint  [SEL]=Select  [FNC]=function  [DEF]=default
   -- ---------------------------------------------------------------------------------------------------------------      
--
--
