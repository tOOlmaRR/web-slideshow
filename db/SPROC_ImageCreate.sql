USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[Image.Select]    Script Date: 2021-05-30 9:08:03 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO







-- =============================================
-- Author:		George U. Marr
-- Create date: May 30, 2021
-- Updates:		
-- Description:	Selects an Image from the database, either by Id or by Full File Path
-- =============================================
CREATE PROCEDURE [dbo].[Image.Select]
	@id int,
	@fullFilePath varchar(500)
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	IF @ID IS NULL
		BEGIN
			SELECT * FROM [dbo].[Images] with (nolock)
			WHERE FullFilePath = @fullFilePath
		END
	ELSE
		BEGIN
			SELECT * FROM [dbo].[Images] with (nolock)
			WHERE ImageID = @id
		END
END
GO


