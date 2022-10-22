USE [WebSlideshow-DEV]
GO

/****** Object:  Table [dbo].[StaticSlideshowImages]    Script Date: 2022-08-21 10:24:53 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[StaticSlideshowImages](
	[StaticSlideshowImageID] [int] IDENTITY(1,1) NOT NULL,
	[StaticSlideshowID] [int] NOT NULL,
	[ImageID] [int] NOT NULL,
	[DisplayOrder] [int] NOT NULL,
 CONSTRAINT [PK_StaticSlideshowImages] PRIMARY KEY CLUSTERED 
(
	[StaticSlideshowImageID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO


