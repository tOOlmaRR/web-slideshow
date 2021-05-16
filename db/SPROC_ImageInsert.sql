USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[Image.Insert]    Script Date: 2021-05-15 8:51:51 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO





-- =============================================
-- Author:		George U. Marr
-- Create date: May 15, 2021
-- Updates:		
-- Description:	Inserts a new Image into the database
-- =============================================
CREATE PROCEDURE [dbo].[Image.Insert]
	@ID int OUTPUT,
	@fullFilePath varchar(250),
	@fileName varchar(50),
	@originalFileName varchar(250),
	@width smallint,
	@height smallint,
	@secure bit
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	INSERT INTO [dbo].[Images] (
		[FullFilePath],
		[FileName],
		[OriginalFileName],
		[Width],
		[Height],
		[Secure]
	)
	VALUES (
		@fullFilePath,
		@fileName,
		@originalFileName,
		@width,
		@height,
		@secure
	)
	SET @ID = SCOPE_IDENTITY()
END
GO


