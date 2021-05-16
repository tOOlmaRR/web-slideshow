USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[TaggedImage.Insert]    Script Date: 2021-05-15 8:53:31 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO






-- =============================================
-- Author:		George U. Marr
-- Create date: May 15, 2021
-- Updates:		
-- Description:	Inserts a new Tag into the database
-- =============================================
CREATE PROCEDURE [dbo].[TaggedImage.Insert]
	@imageID int,
	@tagID int
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	INSERT INTO [dbo].[TaggedImages] (
		[ImageID],
		[TagID]
	)
	VALUES (
		@imageID,
		@tagID
	)
END
GO


