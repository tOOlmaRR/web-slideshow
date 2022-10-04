USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[StaticSlideshows.Select]    Script Date: 2022-08-21 10:31:29 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO





-- =============================================
-- Author:		George U. Marr
-- Create date: July 5, 2022
-- Description:	Selects all Static Slideshows, but only returns non-secure slideshows if specified in the arguements
-- =============================================
CREATE PROCEDURE [dbo].[StaticSlideshows.Select]
	@secureSlideshows bit
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	IF @secureSlideshows = 1
		BEGIN
			SELECT [StaticSlideshowID], [Name], [Secure]
			FROM [dbo].[StaticSlideshows]
			ORDER BY [Name]
		END
	ELSE
		BEGIN
			SELECT [StaticSlideshowID], [Name], [Secure]
			FROM [dbo].[StaticSlideshows]
			WHERE [Secure] = @secureSlideshows
			ORDER BY [Name]
		END
END
GO


