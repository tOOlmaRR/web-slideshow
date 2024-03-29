USE [WebSlideshow-DEV]
GO
/****** Object:  StoredProcedure [dbo].[TaggedImage.Insert]    Script Date: 2021-12-18 8:22:06 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO





-- =============================================
-- Author:		George U. Marr
-- Create date: December 18, 2021
-- Updates:		
-- Description:	Deletes a tag-image mapping from the database
-- =============================================
CREATE PROCEDURE [dbo].[TaggedImage.Delete]
	@imageID int,
	@tagID int
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	DELETE FROM [dbo].[TaggedImages]
	WHERE [ImageID] = @imageID
	AND [TagID] = @tagID
END
