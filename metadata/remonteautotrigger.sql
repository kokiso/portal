ALTER VIEW VGMPREMONTERETORNO AS
  SELECT GMP_CODIGO
         ,CAST(0 AS INTEGER) AS GMP_CODOLD
         ,GMP_CODCNTT  
         ,GMP_CODGM
         ,GMP_CODGP
         ,GMP_CODPE
         ,GMP_CODPEI
         ,GMP_CODFBR
         ,GMP_CODAUT         
         ,GMP_NUMSERIE
         ,GMP_SINCARD
         ,GMP_OPERADORA
         ,GMP_FONE  
         ,GMP_CONTRATO
         ,GMP_CODGML
         ,GMP_DTCONFIGURADO
         ,GMP_DTEMPENHO
         ,GMP_PLACACHASSI
         ,GMP_COMPOSICAO
         ,GMP_STATUS
         ,CAST(0 AS VARCHAR(3)) AS GMP_TIPOEQP
         ,GMP_ACAO         
         ,GMP_CODUSR
    FROM GRUPOMODELOPRODUTO
GO
ALTER TRIGGER dbo.TRGViewGMPREMONTERETORNO_BU ON dbo.VGMPREMONTERETORNO
INSTEAD OF UPDATE
AS
BEGIN
  SET NOCOUNT ON;  
  DECLARE @erroNew VARCHAR(70) = 'OK';  -- Buscando retorno de erro para funcao  
  -------------------
  -- Campos da tabela
  -------------------
  DECLARE @gmpCodigoNew INTEGER;
  DECLARE @gmpCodOldNew INTEGER;
  DECLARE @gmpCodCnttNew INTEGER;  
  DECLARE @gmpCodGmNew INTEGER;
  DECLARE @gmNomeNew VARCHAR(20);
  DECLARE @gmpCodGpNew VARCHAR(3);
  DECLARE @gpNomeNew VARCHAR(20);
  DECLARE @gmpCodPeNew VARCHAR(3);
  DECLARE @peNomeNew VARCHAR(20);
  DECLARE @gmpCodPeiNew INTEGER;
  DECLARE @gmpCodFbrNew INTEGER;
  DECLARE @fvrApelidoNew VARCHAR(15);
  DECLARE @gmpCodAutNew INTEGER;    
  DECLARE @gmpNumSerieNew VARCHAR(20);
  DECLARE @gmpSincardNew VARCHAR(20);
  DECLARE @gmpOperadoraNew VARCHAR(15);
  DECLARE @gmpFoneNew VARCHAR(15);
  DECLARE @gmpContratoNew VARCHAR(10);
  DECLARE @gmpCodGmlNew INTEGER;
  DECLARE @gmpDtConfiguradoNew DATE;
  DECLARE @gmpDtEmpenhoNew DATE;  
  DECLARE @gmpPlacaChassiNew VARCHAR(20);
  DECLARE @gmpComposicaoNew INTEGER;
  DECLARE @gmpAcaoNew INTEGER;
  DECLARE @gmpTipoEqp VARCHAR(3);
  DECLARE @gmpCodUsrNew INTEGER;
  DECLARE @gmpStatusNew INTEGER;
  DECLARE @usrApelidoNew VARCHAR(15);
  DECLARE @usrAdmPubNew VARCHAR(1);
  DECLARE @upD35New INTEGER;
  DECLARE @cntpCod INTEGER;
  DECLARE @cntpPlacaChassi VARCHAR(9);
  DECLARE @gpTipo VARCHAR(3);
  ---------------------------------------------------
  -- Buscando os campos para checagem antes do insert
  ---------------------------------------------------
  SELECT @gmpCodigoNew          = i.GMP_CODIGO
         ,@gmpCodOldNew         = i.GMP_CODOLD   
         ,@gmpCodCnttNew        = i.GMP_CODCNTT
         ,@gmpCodGmNew          = i.GMP_CODGM    
         ,@gmNomeNew            = COALESCE(GM.GM_NOME,'ERRO')         
         ,@gmpCodGpNew          = dbo.fncTranslate(i.GMP_CODGP,3)
         ,@gpNomeNew            = COALESCE(GP.GP_NOME,'ERRO')         
         ,@gmpCodPeNew          = dbo.fncTranslate(i.GMP_CODPE,3)
         ,@peNomeNew            = COALESCE(PE.PE_NOME,'ERRO')
         ,@gmpCodPeiNew         = i.GMP_CODPEI
         ,@gmpCodFbrNew         = i.GMP_CODFBR   
         ,@fvrApelidoNew        = COALESCE(FVR.FVR_APELIDO,'ERRO')
         ,@gmpCodAutNew         = i.GMP_CODAUT         
         ,@gmpNumSerieNew       = dbo.fncTranslate(i.GMP_NUMSERIE,20) 
         ,@gmpSincardNew        = dbo.fncTranslate(i.GMP_SINCARD,20)  
         ,@gmpOperadoraNew      = dbo.fncTranslate(i.GMP_OPERADORA,15)
         ,@gmpFoneNew           = dbo.fncTranslate(i.GMP_FONE,15)
         ,@gmpContratoNew       = dbo.fncTranslate(i.GMP_CONTRATO,10) 
         ,@gmpCodGmlNew         = i.GMP_CODGML   
         ,@gmpDtConfiguradoNew  = i.GMP_DTCONFIGURADO
         ,@gmpDtEmpenhoNew      = i.GMP_DTEMPENHO
         ,@gmpPlacaChassiNew    = dbo.fncTranslate(i.GMP_PLACACHASSI,20)
         ,@gmpComposicaoNew     = i.GMP_COMPOSICAO
         ,@gmpStatusNew         = COALESCE(i.GMP_STATUS,1)
         ,@gmpAcaoNew           = i.GMP_ACAO
         ,@gmpTipoEqp           = i.GMP_TIPOEQP
         ,@gmpCodUsrNew         = i.GMP_CODUSR
         ,@usrApelidoNew        = COALESCE(USR.USR_APELIDO,'ERRO')
         ,@usrAdmPubNew         = COALESCE(USR.USR_ADMPUB,'P')
         ,@upD35New             = UP.UP_D35
    FROM inserted i
    LEFT OUTER JOIN GRUPOMODELO GM ON i.GMP_CODGM=GM.GM_CODIGO AND GM.GM_ATIVO='S'
    LEFT OUTER JOIN GRUPOPRODUTO GP ON i.GMP_CODGP=GP.GP_CODIGO AND GP.GP_ATIVO='S'    
    LEFT OUTER JOIN PONTOESTOQUE PE ON i.GMP_CODPE=PE.PE_CODIGO AND PE.PE_ATIVO='S'    
    LEFT OUTER JOIN FABRICANTE FBR ON i.GMP_CODFBR=FBR.FBR_CODFVR AND FBR.FBR_ATIVO='S'    
    LEFT OUTER JOIN FAVORECIDO FVR ON FBR.FBR_CODFVR=FVR.FVR_CODIGO
    LEFT OUTER JOIN USUARIO USR ON i.GMP_CODUSR=USR.USR_CODIGO AND USR.USR_ATIVO='S'
    LEFT OUTER JOIN USUARIOPERFIL UP ON USR.USR_CODUP=UP.UP_CODIGO;    
  BEGIN TRY        
    -----------------------------
    -- VERIFICANDO A FOREIGN KEYs
    -----------------------------
    IF( @usrApelidoNew='ERRO' )
      RAISERROR('NAO LOCALIZADO USUARIO %i PARA ESTE REGISTRO',15,1,@gmpCodUsrNew);
    IF( @gmNomeNew='ERRO' )
      RAISERROR('NAO LOCALIZADO GRUPO_MODELO %s PARA ESTE REGISTRO',15,1,@gmpCodGmNew);
    IF( @gpNomeNew='ERRO' )
      RAISERROR('NAO LOCALIZADO GRUPO_PRODUTO %s PARA ESTE REGISTRO',15,1,@gmpCodGpNew);
    IF( @peNomeNew='ERRO' )
      RAISERROR('NAO LOCALIZADO PONTO_ESTOQUE %s PARA ESTE REGISTRO',15,1,@gmpCodPeNew);
    ---------------------------  
    -- Auto naum tem fabricante
    ---------------------------  
    IF( @gmpCodGpNew <> 'AUT' ) BEGIN  
      IF( @fvrApelidoNew='ERRO' )
        RAISERROR('NAO LOCALIZADO FABRICANTE %i PARA ESTE REGISTRO',15,1,@gmpCodFbrNew);
    END  
      
    -------------------------------------------------------------
    -- Checando se o usuario tem direito de cadastro nesta tabela
    -------------------------------------------------------------
    IF( @upD35New<3 )
      RAISERROR('USUARIO %s NAO POSSUI DIREITO 35 PARA ALTERAR NA TABELA PRODUTO',15,1,@usrApelidoNew);
    
    SELECT @cntpPlacaChassi = CNTP_PLACACHASSI FROM CONTRATOPRODUTO WHERE CNTP_CODGMP = @gmpCodigoNew;
      IF(@cntpPlacaChassi <> 'NSA0000')
        RAISERROR('AUTO %s INSTALADO NÃO PODE SER REMONTADO',15,1,@gmpCodigoNew);
    --REMONTE
    IF( @gmpAcaoNew = 3 ) BEGIN
      SELECT @gpTipo = GM_GPSERIEOBRIGATORIO FROM GRUPOMODELO WHERE GM_CODIGO = @gmpCodGmNew;
      IF (@gpTipo = @gmpTipoEqp)
        UPDATE GRUPOMODELOPRODUTO SET GMP_NUMSERIE = @gmpNumSerieNew WHERE GMP_CODIGO = @gmpCodigoNew;

      UPDATE GRUPOMODELOPRODUTO SET GMP_CODPE = 'EMP', GMP_CODAUT = @gmpCodigoNew WHERE GMP_CODIGO = @gmpCodigoNew;
      
      UPDATE GRUPOMODELOPRODUTO SET GMP_CODPE = 'EST', GMP_CODAUT = 0 WHERE GMP_CODIGO = @gmpCodOldNew;
    END
    --RETORNO
    IF( @gmpAcaoNew = 4 ) BEGIN
      UPDATE GRUPOMODELOPRODUTO SET GMP_CODPE = 'EST', GMP_CODAUT = 0,GMP_CODCNTT = 0  WHERE GMP_CODAUT = @gmpCodigoNew;
      UPDATE GRUPOMODELOPRODUTO SET GMP_CODPE = 'EST' WHERE GMP_CODIGO = @gmpCodAutNew;

      DELETE FROM VGRUPOMODELOPRODUTO WHERE GMP_CODIGO = @gmpCodigoNew;
    END

    ------------------------------------------------------------------------------------
    -- Se checar até aqui verifico os campos que estão no banco de dados antes de gravar  
    -- Campos OLD da tabela
    ------------------------------------------------------------------------------------
    DECLARE @gmpCodigoOld INTEGER;
    DECLARE @gmpCodCnttOld INTEGER;
    DECLARE @gmpCodGmOld INTEGER;
    DECLARE @gmpCodGpOld VARCHAR(3);
    DECLARE @gmpCodPeOld VARCHAR(3);
    DECLARE @gmpCodPeiOld INTEGER;
    DECLARE @gmpCodFbrOld INTEGER;
    DECLARE @gmpCodAutOld INTEGER;    
    DECLARE @gmpNumSerieOld VARCHAR(20);
    DECLARE @gmpSincardOld VARCHAR(20);
    DECLARE @peSucataOld VARCHAR(1);
    DECLARE @gmpOperadoraOld VARCHAR(15);
    DECLARE @gmpFoneOld VARCHAR(15);
    DECLARE @gmpContratoOld VARCHAR(10);
    DECLARE @gmpCodGmlOld INTEGER;
    DECLARE @gmpDtConfiguradoOld DATE;
    DECLARE @gmpDtEmpenhoOld DATE;    
    DECLARE @gmpPlacaChassiOld VARCHAR(20);
    DECLARE @gmpComposicaoOld INTEGER;
    DECLARE @gmpStatusOld INTEGER;
    DECLARE @gmpCodUsrOld INTEGER;
    
    SELECT @gmpCodigoOld          = d.GMP_CODIGO   
           ,@gmpCodCnttOld        = d.GMP_CODCNTT
           ,@gmpCodGmOld          = d.GMP_CODGM    
           ,@gmpCodGpOld          = d.GMP_CODGP
           ,@gmpCodPeOld          = d.GMP_CODPE
           ,@peSucataOld          = COALESCE(PE.PE_SUCATA,'N')
           ,@gmpCodPeiOld         = d.GMP_CODPEI
           ,@gmpCodFbrOld         = d.GMP_CODFBR   
           ,@gmpCodAutOld         = d.GMP_CODAUT                    
           ,@gmpNumSerieOld       = d.GMP_NUMSERIE
           ,@gmpSincardOld        = d.GMP_SINCARD
           ,@gmpOperadoraOld      = d.GMP_OPERADORA
           ,@gmpFoneOld           = d.GMP_FONE
           ,@gmpContratoOld       = d.GMP_CONTRATO
           ,@gmpCodGmlOld         = d.GMP_CODGML   
           ,@gmpDtConfiguradoOld  = d.GMP_DTCONFIGURADO
           ,@gmpDtEmpenhoOld      = d.GMP_DTEMPENHO
           ,@gmpPlacaChassiOld    = d.GMP_PLACACHASSI
           ,@gmpComposicaoOld     = d.GMP_COMPOSICAO
           ,@gmpStatusOld         = d.GMP_STATUS
           ,@gmpCodUsrOld         = d.GMP_CODUSR
      FROM GRUPOMODELOPRODUTO d 
      LEFT OUTER JOIN PONTOESTOQUE PE ON d.GMP_CODPE=PE.PE_CODIGO
     WHERE d.GMP_CODIGO=@gmpCodAutNew;  
    ---------------------------------------------------------------------
    -- Primary Key nao pode ser alterada
    ---------------------------------------------------------------------
    IF( @gmpCodigoOld<>@gmpCodigoNew )
      RAISERROR('CAMPO CODIGO NAO PODE SER ALTERADO',15,1);  
    IF( @gmpCodGmOld<>@gmpCodGmNew )
      RAISERROR('CAMPO MODELO NAO PODE SER ALTERADO',15,1);  
    IF( @gmpCodGpOld<>@gmpCodGpNew )
      RAISERROR('CAMPO GRUPO NAO PODE SER ALTERADO',15,1);  
    IF( @gmpCodFbrOld<>@gmpCodFbrNew )
      RAISERROR('CAMPO FABRICANTE NAO PODE SER ALTERADO',15,1);  
    IF( @gmpCodGmlOld<>@gmpCodGmlNew )
      RAISERROR('CAMPO LOTE NAO PODE SER ALTERADO',15,1);  
    --  
    ---------------  
    -- Gravando LOG
    ---------------
   IF( (@gmpCodCnttOld<>@gmpCodCnttNew) OR (@gmpCodPeOld<>@gmpCodPeNew) OR (@gmpCodPeiOld<>@gmpCodPeiNew) OR (@gmpCodAutOld<>@gmpCodAutNew) 
    OR (@gmpNumSerieOld<>@gmpNumSerieNew) OR (@gmpSincardOld<>@gmpSincardNew)OR (@gmpStatusNew<>@gmpStatusOld) OR (@gmpOperadoraOld<>@gmpOperadoraNew) OR (@gmpFoneOld<>@gmpFoneNew) 
    OR (@gmpContratoOld<>@gmpContratoNew) 
    OR ((@gmpDtConfiguradoOld IS NULL) AND (@gmpDtConfiguradoNew IS NOT NULL))
    OR ((@gmpDtConfiguradoOld IS NOT NULL) AND (@gmpDtConfiguradoNew IS NULL))
    OR ((@gmpDtEmpenhoOld IS NULL) AND (@gmpDtEmpenhoNew IS NOT NULL))
    OR ((@gmpDtEmpenhoOld IS NOT NULL) AND (@gmpDtEmpenhoNew IS NULL))
    OR (@gmpPlacaChassiOld<>@gmpPlacaChassiNew) OR (@gmpComposicaoOld<>@gmpComposicaoNew)
    OR (@gmpCodUsrOld<>@gmpCodUsrNew) ) BEGIN
      INSERT INTO dbo.BKPGRUPOMODELOPRODUTO(
        GMP_ACAO
        ,GMP_CODIGO
        ,GMP_CODCNTT        
        ,GMP_CODGM
        ,GMP_CODGP
        ,GMP_CODPE
        ,GMP_CODPEI
        ,GMP_CODAUT
        ,GMP_CODFBR
        ,GMP_NUMSERIE
        ,GMP_SINCARD
        ,GMP_OPERADORA
        ,GMP_FONE
        ,GMP_CONTRATO
        ,GMP_CODGML
        ,GMP_DTCONFIGURADO
        ,GMP_DTEMPENHO        
        ,GMP_PLACACHASSI
        ,GMP_COMPOSICAO
        ,GMP_STATUS
        ,GMP_CODUSR) VALUES(
        'A'                    -- GMP_ACAO
        ,@gmpCodigoNew         -- GMP_CODIGO         
        ,@gmpCodCnttNew        -- GMP_CODCNTT        
        ,@gmpCodGmNew          -- GMP_CODGM         
        ,@gmpCodGpNew          -- GMP_CODGP
        ,@gmpCodPeNew          -- GMP_CODPE
        ,@gmpCodPeiNew         -- GMP_CODPEI
        ,@gmpCodFbrNew         -- GMP_CODFBR        
        ,@gmpCodAutNew         -- GMP_CODAUT
        ,@gmpNumSerieNew       -- GMP_NUMSERIE
        ,@gmpSincardNew        -- GMP_SINCARD
        ,@gmpOperadoraNew      -- GMP_OPERADORA
        ,@gmpFoneNew           -- GMP_FONE
        ,@gmpContratoNew       -- GMP_CONTRATO
        ,@gmpCodGmlNew         -- GMP_CODGML        
        ,@gmpDtConfiguradoNew  -- GMP_DTCONFIGURADO
        ,@gmpDtEmpenhoNew      -- GMP_DTEMPENHO
        ,@gmpPlacaChassiNew    -- GMP_PLACACHASSI
        ,@gmpComposicaoNew     -- GMP_COMPOSICAO
        ,@gmpStatusNew
        ,@gmpCodUsrNew         -- GMP_CODUSR
      );  
    END
  END TRY
  BEGIN CATCH
    DECLARE @ErrorMessage NVARCHAR(4000);
    DECLARE @ErrorSeverity INT;
    DECLARE @ErrorState INT;
    SELECT @ErrorMessage=ERROR_MESSAGE(),@ErrorSeverity=ERROR_SEVERITY(),@ErrorState=ERROR_STATE();
    RAISERROR(@ErrorMessage, @ErrorSeverity, @ErrorState);
    RETURN;
  END CATCH
END