USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[Tags.Select]    Script Date: 2021-09-15 9:40:01 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO







-- =============================================
-- Author:		George U. Marr
-- Create date: September 15, 2021
-- Updates:		
-- Description:	Selects all Tags from the database
-- =============================================
CREATE PROCEDURE [dbo].[Tags.Select]
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	BEGIN
		SELECT * FROM [dbo].[Tags] with (nolock)
	END

END
GO


