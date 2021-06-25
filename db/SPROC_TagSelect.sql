USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[Tag.Select]    Script Date: 2021-06-25 5:11:19 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO






-- =============================================
-- Author:		George U. Marr
-- Create date: May 22, 2021
-- Updates:		
-- Description:	Selects a Tag from the database, either by Id or by Tag
-- =============================================
CREATE PROCEDURE [dbo].[Tag.Select]
	@ID int,
	@tag varchar(50)
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	IF @ID IS NULL
		BEGIN
			SELECT * FROM [dbo].[Tags] with (nolock)
			WHERE Tag = @tag
		END
	ELSE
		BEGIN
			SELECT * FROM [dbo].[Tags] with (nolock)
			WHERE TagID = @ID
		END
END
GO


