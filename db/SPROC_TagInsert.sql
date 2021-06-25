USE [WebSlideshow-DEV]
GO

/****** Object:  StoredProcedure [dbo].[Tag.Insert]    Script Date: 2021-05-15 8:52:51 PM ******/
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
CREATE PROCEDURE [dbo].[Tag.Insert]
	@ID int OUTPUT,
	@tag varchar(50),
	@secure bit
AS
BEGIN
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;

	INSERT INTO [dbo].[Tags] (
		[Tag],
		[Secure]
	)
	VALUES (
		@tag,
		@secure
	)
	SET @ID = SCOPE_IDENTITY()
END
GO


