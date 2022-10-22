USE [WebSlideshow-DEV]
GO

/****** Object:  Table [dbo].[StaticSlideshows]    Script Date: 2022-08-21 10:27:04 AM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[StaticSlideshows](
	[StaticSlideshowID] [int] IDENTITY(1,1) NOT NULL,
	[Name] [varchar](100) NULL,
	[Secure] [bit] NULL,
 CONSTRAINT [PK_StaticSlideshows] PRIMARY KEY CLUSTERED 
(
	[StaticSlideshowID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO


