USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[Tags.Select]    Script Date: 2021-12-17 9:41:34 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO









-- =============================================
-- Author:		George U. Marr
-- Create date: September 15, 2021
-- Updates:		December 17, 2021 (add parameters to allow retrieving tags for a given image, and only non-secure tags if requested)
-- Description:	Selects all Tags from the database
-- =============================================
ALTER PROCEDURE [dbo].[Tags.Select]
	@imageID int,
	@secureTags bit
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	IF @imageID IS NOT NULL
		BEGIN
			IF @secureTags = 1
				BEGIN
					SELECT t.TagID, t.Tag, t.Secure FROM [dbo].[Tags] t with (nolock)
					JOIN [dbo].[TaggedImages] ti  with (nolock)
						ON (t.TagID = ti.TagID)
					WHERE ti.ImageID = @imageID
					ORDER BY t.Tag
				END
			ELSE
				BEGIN
					SELECT t.TagID, t.Tag, t.Secure FROM [dbo].[Tags] t with (nolock)
					JOIN [dbo].[TaggedImages] ti  with (nolock)
						ON (t.TagID = ti.TagID)
					WHERE ti.ImageID = @imageID
					AND t.Secure = 0
					ORDER BY t.Tag
				END
		END
	ELSE
		BEGIN
			IF @secureTags = 1
				BEGIN
					SELECT t.TagID, t.Tag, t.Secure FROM [dbo].[Tags] t with (nolock)
					ORDER BY t.Tag
				END
			ELSE
				BEGIN
					SELECT t.TagID, t.Tag, t.Secure FROM [dbo].[Tags] t with (nolock)
					WHERE t.Secure = 0
					ORDER BY t.Tag
				END
		END
END
GO


