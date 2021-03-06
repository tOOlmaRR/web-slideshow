USE [WebSlideshow-DEV]
GO
/****** Object:  User [Urgele1]    Script Date: 2021-05-22 6:00:40 PM ******/
CREATE USER [Urgele1] FOR LOGIN [Urgele1] WITH DEFAULT_SCHEMA=[db_datareader]
GO
/****** Object:  Table [dbo].[Images]    Script Date: 2021-05-22 6:00:40 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Images](
	[ImageID] [int] IDENTITY(1,1) NOT NULL,
	[FullFilePath] [varchar](500) NOT NULL,
	[FileName] [varchar](200) NOT NULL,
	[OriginalFileName] [varchar](200) NULL,
	[Width] [smallint] NULL,
	[Height] [smallint] NULL,
	[Secure] [bit] NOT NULL,
 CONSTRAINT [PK_Images] PRIMARY KEY CLUSTERED 
(
	[ImageID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[TaggedImages]    Script Date: 2021-05-22 6:00:41 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[TaggedImages](
	[ImageID] [int] NOT NULL,
	[TagID] [int] NOT NULL,
 CONSTRAINT [PK_Tagged_Images] PRIMARY KEY CLUSTERED 
(
	[ImageID] ASC,
	[TagID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Tags]    Script Date: 2021-05-22 6:00:41 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Tags](
	[TagID] [int] IDENTITY(1,1) NOT NULL,
	[Tag] [varchar](50) NOT NULL,
	[Secure] [bit] NOT NULL,
 CONSTRAINT [PK_Tags] PRIMARY KEY CLUSTERED 
(
	[TagID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]
GO
