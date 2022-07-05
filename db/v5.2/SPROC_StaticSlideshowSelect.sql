USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[StaticSlideshow.Select]    Script Date: 2022-07-05 7:48:45 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO




-- =============================================
-- Author:		George U. Marr
-- Create date: July 5, 2022
-- Description:	Selects all Images in the requested Static Slideshow in the predefined order
-- =============================================
CREATE PROCEDURE [dbo].[StaticSlideshow.Select]
	@staticSlideshowID int,
	@secureImages bit
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	IF @secureImages = 1
		BEGIN
			SELECT Images.ImageID, Images.FullFilePath, Images.FileName, Images.width, Images.height, images.Secure, StaticSlideshowImages.DisplayOrder
			FROM Images
			JOIN StaticSlideshowImages ON Images.ImageID = StaticSlideshowImages.ImageID
			WHERE StaticSlideshowImages.StaticSlideshowID = @staticSlideshowID
			ORDER BY StaticSlideshowImages.DisplayOrder
		END
	ELSE
		BEGIN
			SELECT Images.ImageID, Images.FullFilePath, Images.FileName, Images.width, Images.height, images.Secure, StaticSlideshowImages.DisplayOrder
			FROM Images
			JOIN StaticSlideshowImages ON Images.ImageID = StaticSlideshowImages.ImageID
			WHERE StaticSlideshowImages.StaticSlideshowID = @staticSlideshowID
			AND Images.Secure = @secureImages
			ORDER BY StaticSlideshowImages.DisplayOrder
		END
END
GO


