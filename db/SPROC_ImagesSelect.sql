USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[Images.Select]    Script Date: 2021-09-21 10:57:02 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO








-- =============================================
-- Author:		George U. Marr
-- Create date: September 21, 2021
-- Description:	Selects all Images and Tags information given a list of tags, but only return non-secure images 
--              if specified in the arguements
-- =============================================
CREATE PROCEDURE [dbo].[Images.Select]
	@tag varchar(50),
	@secureImages bit
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	IF @secureImages = 1
		BEGIN
			SELECT Images.ImageID, Images.FullFilePath, Images.FileName, Images.width, Images.height, images.Secure
			FROM Images
			JOIN TaggedImages ON Images.ImageID = TaggedImages.ImageID
			JOIN Tags ON TaggedImages.TagID = Tags.TagID
			WHERE Tags.Tag = @tag			
		END
	ELSE
		BEGIN
			SELECT Images.ImageID, Images.FullFilePath, Images.FileName, Images.width, Images.height, images.Secure
			FROM Images
			JOIN TaggedImages ON Images.ImageID = TaggedImages.ImageID
			JOIN Tags ON TaggedImages.TagID = Tags.TagID
			WHERE Tags.Tag = @tag
			AND Images.Secure = @secureImages
		END
END
GO


